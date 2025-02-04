<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\PaymentModel;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    private $debugMode;

    public function __construct()
    {
        $this->debugMode = config('constants.debug_mode');
    }

    public function index(Request $request)
    {
        $customer = (int) $request->get('customer', NULL);
        $product = $request->get('product', NULL);
        $status = $request->get('status', NULL);
        $createdAt = $request->get('created_on', NULL);

        $customers = User::select([
            'userId',
            'name'
        ])
            ->where([
                'roleId' => CUSTOMER,
                'status' => ACTIVE
            ])
            ->get();

        $products = Product::select([
            'id',
            'name'
        ])
            ->where('status', ACTIVE)
            ->get();

        $query = Order::select([
            'id',
            'user_id',
            'product_id',
            'payment_method_id',
            'quantity',
            'total_amount',
            'paid_amount',
            'due_amount',
            'status',
            'created_at'
        ]);

        if (!empty($customer)) {
            $query->where('user_id', $customer);
        }

        if (!empty($product)) {
            $query->where('product_id', $product);
        }

        if (isset($status)) {
            $query->where('status', $status);
        }

        if (!empty($createdAt)) {
            $query->whereDate('created_at', $createdAt);
        }

        $query->with('product', function ($query) {
            $query->select('id', 'name');
        })
            ->with('user', function ($query) {
                $query->select('userId', 'name');
            })
            ->with('paymentMethod', function ($query) {
                $query->select('id', 'pay_mod');
            });

        $orders = $query->orderBy('id', 'DESC')
            ->paginate(PER_PAGE);

        return view('admin.orders.index', [
            'customers' => $customers,
            'orders' => $orders,
            'products' => $products
        ]);
    }

    public function create()
    {
        $customers = User::select([
            'userId',
            'name'
        ])
            ->where([
                'roleId' => 2,
                'status' => ACTIVE
            ])
            ->get();

        $products = Product::select([
            'id',
            'name'
        ])
            ->where('status', ACTIVE)
            ->get();

        $paymentMethods = PaymentModel::select([
            'id',
            'pay_mod'
        ])
            ->get();

        return view('admin.orders.create', [
            'customers' => $customers,
            'products' => $products,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Store Orders
     * 
     * @param \App\Http\Requests\OrderRequest;
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(OrderRequest $request)
    {
        try {
            $dates = $this->parseBookingDateRange($request->input('booking_date_range'));

            list($totalAmount, $paidAmount, $dueAmount) = $this->calculateAmountDetails(
                $request->input('total_amount'),
                $request->input('paid_amount')
            );

            $order = new Order();
            $order->fill([
                'order_id' => $this->generateUniqueOrderId(),
                'user_id' => $request->input('customer'),
                'product_id' => $request->input('product'),
                'payment_method_id' => $request->input('payment_method'),
                'quantity' => $request->input('quantity'),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'due_date' => $request->input('due_date'),
                'status' => (int)$request->input('status'),
                'delivered_by' => (int)$request->input('delivered_by'),
                'booking_date_from' => $dates['start'],
                'booking_date_to' => $dates['end'],
                'remarks' => $request->input('remarks', '')
            ]);

            if ($order->save()) {
                return redirect()->route('admin.orders.index')->with('success', 'Order created successfully');
            }

            return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Order NOT created.'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', showErrorMessage($this->debugMode, $e->getMessage()));
        }
    }

    public function edit($id)
    {

        if (!empty($id)) {
            $order = Order::find($id);

            $customers = User::select([
                'userId',
                'name'
            ])
                ->where([
                    'roleId' => 2,
                    'status' => ACTIVE
                ])
                ->get();

            $products = Product::select([
                'id',
                'name'
            ])
                ->where('status', ACTIVE)
                ->get();

            $paymentMethods = PaymentModel::select([
                'id',
                'pay_mod'
            ])
                ->get();

            return view('admin.orders.edit', [
                'order' => $order,
                'customers' => $customers,
                'products' => $products,
                'paymentMethods' => $paymentMethods
            ]);
        }

        return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Id not found'));
    }

    /**
     * Update a newly created product in storage.
     * 
     * @param \App\Http\Requests\ProductRequest $request
     * @param \App\Models\Product $product
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OrderRequest $request, $id)
    {

        $isUpdated = false;
        $oldData = [];
        $newData = [];

        if (empty($id)) {
            return redirect()->route('admin.products.index')->with('error', showErrorMessage($this->debugMode, 'Id not found'));
        }

        $dates = $this->parseBookingDateRange($request->input('booking_date_range'));

        list($totalAmount, $paidAmount, $dueAmount) = $this->calculateAmountDetails(
            $request->input('total_amount'),
            $request->input('paid_amount')
        );

        try {
            $order = Order::find($id);
            $oldData = $this->getOrderLogDataArray($order);

            if ($request->has('payment_method')) {
                $order->payment_method_id = $request->input('payment_method');
            }
            if ($request->has('quantity')) {
                $order->quantity = $request->input('quantity');
            }
            if ($request->has('booking_date_range')) {
                $order->booking_date_from = $dates['start'];
                $order->booking_date_to = $dates['end'];
            }
            if (isset($totalAmount)) {
                $order->total_amount = $totalAmount;
            }
            if (isset($paidAmount)) {
                $order->paid_amount = $paidAmount;
            }
            if (isset($dueAmount)) {
                $order->due_amount = $dueAmount;
            }
            if ($request->has('due_date')) {
                $order->due_date = $request->input('due_date');
            }
            if ($request->has('status')) {
                $order->status = $request->input('status');
            }
            if ($request->has('delivered_by')) {
                $order->delivered_by = $request->input('delivered_by');
            }
            if ($request->has('remarks')) {
                $order->remarks = $request->input('remarks');
            }

            $isUpdated = $order->update();

            if (!$isUpdated) {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Order not updated.'));
            }

            if ($isUpdated) {
                $newData = $this->getOrderLogDataArray($order);
                $this->createOrderLogs($id, $oldData, $newData);
            }

            return redirect()->route('admin.orders.index')->with('success', 'Product updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', showErrorMessage($this->debugMode, $e->getMessage()));
        }
    }

    /**
     * Delete Order
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if ($order) {
            $this->createOrderLogs($order->id, $this->getOrderLogDataArray($order), [
                'deleted_at' => date('Y-m-d H:i:s') // Corrected format
            ]);

            $order->delete(); // Ensure the order is actually deleted

            return redirect()->back()->with('success', 'Order deleted successfully.');
        }

        return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Failed to delete order.'));
    }


    /**
     * Function to handle booking date range parsing
     */
    private function parseBookingDateRange($bookingDateRange)
    {
        $dates = explode(' - ', $bookingDateRange);
        return [
            'start' => $dates[0] ?? null,
            'end' => $dates[1] ?? null
        ];
    }

    /**
     * Function to calculate due amount
     */
    private function calculateAmountDetails($totalAmount, $paidAmount)
    {
        $totalAmount = floatval($totalAmount);
        $paidAmount = floatval($paidAmount);
        $dueAmount = ($totalAmount > $paidAmount) ? ($totalAmount - $paidAmount) : 0;

        return [$totalAmount, $paidAmount, $dueAmount];
    }

    private function generateUniqueOrderId()
    {
        do {
            $orderId = strtoupper(Str::random(10)); // Generates a random 10-character uppercase string
        } while (Order::where('order_id', $orderId)->exists()); // Ensure uniqueness

        return $orderId;
    }

    private function getOrderLogDataArray($order)
    {
        return [
            'order_id' => $order->order_id ?? '',
            'user_id' => $order->user_id ?? '',
            'product_id' => $order->product_id ?? '',
            'payment_method_id' => $order->payment_method_id ?? '',
            'quantity' => $order->quantity ?? '',
            'total_amount' => $order->total_amount ?? '',
            'paid_amount' => $order->paid_amount ?? '',
            'due_amount' => $order->due_amount ?? '',
            'due_date' => $order->due_date ?? '',
            'status' => $order->status ?? '',
            'delivered_by' => $order->delivered_by ?? '',
            'booking_date_from' => $order->booking_date_from ?? '',
            'booking_date_to' => $order->booking_date_to ?? '',
            'remarks' => $order->remarks ?? '',
            'created_at' => $order->created_at ?? '',
            'updated_at' => $order->updated_at ?? ''
        ];
    }

    private function createOrderLogs($orderId, $oldData = [], $newData = [])
    {
        if ((count($oldData) <= 0) || (count($newData) <= 0) || empty($orderId)) {
            return false;
        }

        $createdOrderLog = OrderLog::create([
            'order_id' => $orderId,
            'old_data' => json_encode($oldData),
            'new_data' => json_encode($newData)
        ]);

        return $createdOrderLog;
    }
}
