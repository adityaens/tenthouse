<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'userId' => 'required|exists:tbl_users,userId',
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Extract request data
        $userId = $request->userId;
        $productId = $request->productId;
        $quantity = $request->quantity;

        $product = Product::find($productId);
        $remQty = $product->rem_qty ?? 0;
        if($remQty == 0 || $remQty < $quantity) {
            return response()->json([
                'success' => false,
                'error' => 'Product NOT available'
            ], 206);
        }

        // Check if the product already exists in the cart for this user
        $cart = Cart::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($cart) {
            // Update existing cart item
            $cart->quantity = $quantity;
            $cart->save();
        } else {
            // Insert new cart item
            $cart = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        // Fetch updated cart item with product and user details
        $cart = Cart::with(['product', 'user'])->find($cart->id);

        return response()->json([
            'success' => true,
            'message' => $cart->wasRecentlyCreated ? 'Product added to cart' : 'Cart updated successfully',
            'cart' => $cart,
            'price' => $cart->price
        ], 200);
    }
}
