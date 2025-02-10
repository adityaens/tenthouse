<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\OrderProduct;
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
            'name',
        ])
            ->where('status', ACTIVE)
            ->get();

        $query = Order::select([
            'id',
            'user_id',
            'payment_method_id',
            'quantity',
            'total_amount',
            'paid_amount',
            'due_amount',
            'delivered_by',
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
            })
            ->with('orderProducts', function ($query) {
                $query->select('*');
            })
            ->with('user.groups', function ($query) {
                $query->select('*');
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
            'name',
        ])
            ->where([
                'roleId' => 2,
                'status' => ACTIVE
            ])
            ->get();

        $products = Product::select([
            'id',
            'name',
            'price',
            'rem_qty'
        ])
            ->where('status', ACTIVE)
            ->where('rem_qty', '>', 0)
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

    public function createUpdate($id)
    {
        $customers = User::select([
            'userId',
            'name',
        ])
            ->where([
                'roleId' => 2,
                'status' => ACTIVE
            ])
            ->get();

        $products = Product::select([
            'id',
            'name',
            'price',
            'rem_qty'
        ])
            ->where('status', ACTIVE)
            ->where('rem_qty', '>', 0)
            ->get();

        $paymentMethods = PaymentModel::select([
            'id',
            'pay_mod'
        ])
            ->get();

        $order = Order::with('orderProducts', 'user')
            ->where('id', $id)
            ->first();

;
        return view('admin.orders.createUpdate', [
            'order' => $order,
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
    public function store(Request $request)
    {
        try {
            $totalPrice = 0;
            $totalQty = 0;
            $userId = $request->input('userId');
            if (!$request->has('userId')) {
                return response()->json([
                    'success' => false,
                    'error' => showErrorMessage($this->debugMode, 'User NOT found.'),
                ]);
            }

            $orderProducts = json_decode($request->input('cartItems'), true);

            $order = Order::create([
                'order_id' => $this->generateUniqueOrderId(),
                'user_id' => $userId
            ]);

            if ($order) {
                foreach ($orderProducts as $orderProduct) {
                    // Ensure that unitPrice and quantity are properly converted to float
                    $unitPrice = (int)$orderProduct['unitPrice'];
                    $quantity = (int)$orderProduct['quantity'];

                    // Calculate total price for this product and update totalPrice
                    $productTotalPrice = $unitPrice * $quantity;
                    $totalPrice += $productTotalPrice;
                    $totalQty += $quantity;

                    OrderProduct::create([
                        'order_id' => $order->id,
                        'product_id' => $orderProduct['productId'],
                        'product_name' => $orderProduct['productName'],
                        'sku' => $orderProduct['sku'],
                        'unit_price' => $orderProduct['unitPrice'],
                        'quantity' => $orderProduct['quantity'],
                        'total_price' => $orderProduct['totalPrice']
                    ]);

                    $product = Product::find($orderProduct['productId']);
                    $usedQty = (int)$product->used_qty;
                    $usedQty += $quantity;
                    $remQty = (int)$product->rem_qty;
                    $remQty -= $quantity;

                    $product->used_qty = $usedQty;
                    $product->rem_qty = $remQty;
                    $product->update();
                }
                $order->total_amount = $totalPrice;
                $order->quantity = $totalQty;
                $order->update();
                Cart::truncate();
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Order NOT created.'
                ]);
            }

            return response()->json([
                'success' => true,
                'orderId' => $order->id,
                'message' => 'Order created.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    /**
     * Store Orders 
     * 
     * @param \App\Http\Requests\OrderRequest;
     * @param int $id;
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUpdate(Request $request, $id)
    {
        try {
            $totalPrice = 0;
            $totalQty = 0;
            $orderTotal = 0;
            $orderQty = 0;
            $userId = $request->input('userId');
            if (!$request->has('userId')) {
                return response()->json([
                    'success' => false,
                    'error' => showErrorMessage($this->debugMode, 'User NOT found.'),
                ]);
            }

            $orderProducts = json_decode($request->input('cartItems'), true);

            foreach ($orderProducts as $orderProduct) {
                // Ensure that unitPrice and quantity are properly converted to float
                $unitPrice = (int)$orderProduct['unitPrice'];
                $quantity = (int)$orderProduct['quantity'];

                // Calculate total price for this product and update totalPrice
                $productTotalPrice = $unitPrice * $quantity;
                $totalPrice += (int)$orderProduct['totalPrice'];
                $totalQty += (int)$orderProduct['quantity'];

                //For order
                $orderTotal += $productTotalPrice;
                $orderQty += $quantity;

                $orderProductDb = OrderProduct::where([
                    'order_id' => $id,
                    'product_id' => $orderProduct['productId']
                ])
                    ->first();

                if ($orderProductDb) {
                    $orderProductDb->update([
                        'quantity' => (int)$orderProductDb->quantity + (int)$orderProduct['quantity'],
                        'total_price' => (int)$orderProductDb->total_price + (int)$orderProduct['totalPrice']
                    ]);
                } else {
                    OrderProduct::create([
                        'order_id' => $id,
                        'product_id' => $orderProduct['productId'],
                        'product_name' => $orderProduct['productName'],
                        'sku' => $orderProduct['sku'],
                        'unit_price' => $orderProduct['unitPrice'],
                        'quantity' => $orderProduct['quantity'],
                        'total_price' => $orderProduct['totalPrice']
                    ]);
                }

                //Updating Product Data
                $product = Product::find($orderProduct['productId']);
                $usedQty = (int)$product->used_qty;
                $usedQty += $quantity;
                $remQty = (int)$product->rem_qty;
                $remQty -= $quantity;

                $product->used_qty = $usedQty;
                $product->rem_qty = $remQty;
                $product->update();
            }

            //Updating Order data
            $order = Order::find($id);
            $oldQty = $order->quantity;
            $oldTotalPrice = $order->total_amount;

            $order->quantity = $oldQty + $orderQty;
            $order->total_amount = $oldTotalPrice + $orderTotal;
            $order->update();
            
            Cart::truncate();

            return response()->json([
                'success' => true,
                'orderId' => $id,
                'message' => 'Order created.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {

        if (!empty($id)) {
            $order = Order::find($id);

            if ($order) {
                $paymentMethods = PaymentModel::select([
                    'id',
                    'pay_mod'
                ])
                    ->get();

                return view('admin.orders.edit', [
                    'order' => $order,
                    'paymentMethods' => $paymentMethods
                ]);
            }
        }

        return redirect()->route('admin.orders.index')->with('error', showErrorMessage($this->debugMode, 'Order not found'));
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
        $discount = 0;
        $oldData = [];
        $newData = [];

        if (empty($id)) {
            return redirect()->route('admin.orders.index')->with('error', showErrorMessage($this->debugMode, 'Id not found'));
        }

        $dates = $this->parseBookingDateRange($request->input('booking_date_range'));

        try {
            $order = Order::find($id);

            if (($request->input('due_amount') < 0) || ($request->input('paid_amount') < 0)) {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Due/Paid amount less than Zero'));
            }

            $oldData = $this->getOrderLogDataArray($order);

            if ($request->has('payment_method')) {
                $order->payment_method_id = $request->input('payment_method');
            }
            if ($request->has('booking_date_range')) {
                $order->booking_date_from = $dates['start'];
                $order->booking_date_to = $dates['end'];
            }
            if ($request->has('paid_amount')) {
                $order->paid_amount = $request->input('paid_amount');
            }
            if ($request->has('due_amount')) {
                $order->due_amount = $request->input('due_amount');
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

            return redirect()->route('admin.orders.index')->with('success', 'Order modified successfully.');
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
    private function calculateAmountDetails($totalAmount, $paidAmount, $discount)
    {
        $totalAmount = floatval($totalAmount) * (1 - ($discount / 100));
        $paidAmount = floatval($paidAmount);
        $dueAmount = max($totalAmount - $paidAmount, 0); // Ensures due amount is never negative

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

    public function view($id)
    {
        if (!empty($id)) {
            $query = Order::select([
                'id',
                'order_id',
                'user_id',
                'payment_method_id',
                'quantity',
                'total_amount',
                'paid_amount',
                'due_amount',
                'delivered_by',
                'status',
                'created_at'
            ]);

            $query->with('product', function ($query) {
                $query->select('id', 'name');
            })
                ->with('user', function ($query) {
                    $query->select('userId', 'name');
                })
                ->with('paymentMethod', function ($query) {
                    $query->select('id', 'pay_mod');
                })
                ->with('orderProducts', function ($query) {
                    $query->select('*');
                })
                ->with('user.groups', function ($query) {
                    $query->select('*');
                });

            $order = $query->where('id', $id)
                ->first();

            return view('admin.orders.view', [
                'order' => $order
            ]);
        } else {
            return redirect()->route('admin.orders.index')->with('error', showErrorMessage($this->debugMode, 'Order not found'));
        }
    }

    public function createOtherDetails(Request $request)
    {
        $isUpdated = false;
        $id = $request->input('orderId');
        $oldData = [];
        $newData = [];

        if (empty($id)) {
            return redirect()->route('admin.orders.index')->with('error', showErrorMessage($this->debugMode, 'Id not found'));
        }

        $dates = $this->parseBookingDateRange($request->input('booking_date_range'));

        try {
            $order = Order::find($id);

            if (($request->input('due_amount') < 0) || ($request->input('paid_amount') < 0)) {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Due/Paid amount less than Zero'));
            }

            $oldData = $this->getOrderLogDataArray($order);

            if ($request->has('payment_method')) {
                $order->payment_method_id = $request->input('payment_method');
            }
            if ($request->has('booking_date_range')) {
                $order->booking_date_from = $dates['start'];
                $order->booking_date_to = $dates['end'];
            }
            if ($request->has('paid_amount')) {
                $order->paid_amount = $request->input('paid_amount');
            }
            if ($request->has('due_amount')) {
                $order->due_amount = $request->input('due_amount');
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

            return redirect()->route('admin.orders.index')->with('success', 'Order modified successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', showErrorMessage($this->debugMode, $e->getMessage()));
        }
    }
    public function removeProduct($id){
        $product=OrderProduct::find($id);
        $productDetails=Product::find($product->product_id);
        $productDetails->used_qty -= $product->quantity;        
        $productDetails->rem_qty += $product->quantity;
        $productDetails->update();
        
        if($product->delete()){
            return redirect()->route('admin.orders.index')->with('success', 'Order modified successfully.');
        }
    }
}
