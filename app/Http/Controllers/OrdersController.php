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
            if ($totalAmount) {
                $order->total_amount = $totalAmount;
            }
            if ($paidAmount) {
                $order->paid_amount = $paidAmount;
            }
            if ($dueAmount) {
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
                // $logs = $this->createOrderLogs($id, $oldData, $newData);
            }

            return redirect()->route('admin.orders.index')->with('success', 'Product updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', showErrorMessage($this->debugMode, $e->getMessage()));
        }
    }

    // private function createOrderLogs($orderId, $oldData = [], $newData = [])
    // {
    //     if(empty($orderId)) {
    //         return false;
    //     }

    //     $createdOrderLog = OrderLog::create([
    //         'order_id' => $orderId,
    //         'old_data' => json_encode($oldData),
    //         'new_data' => json_encode($newData)
    //     ]);

    //     return $createdOrderLog;
    // }
}
