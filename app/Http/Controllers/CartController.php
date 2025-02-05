<?php

namespace App\Http\Controllers;

use App\Models\Cart;
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
        $cart = Cart::with(['product', 'user', 'user.group'])->find($cart->id);

        return response()->json([
            'message' => $cart->wasRecentlyCreated ? 'Product added to cart' : 'Cart updated successfully',
            'cart' => $cart,
            'price' => $cart->price
        ], 200);
    }
}
