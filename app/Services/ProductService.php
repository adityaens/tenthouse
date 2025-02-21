<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class ProductService
{
    /**
     * Select Product Data
     */
    public function getProducts($request)
    {
        $searchName = $request->get('name', NULL);
        $searchCatId = $request->get('cat_id', NULL);
        $searchStatus = $request->get('status', NULL);
        $searchCreatedAt = $request->get('created_at', NULL);

        $query = Product::select([
            'id',
            'name',
            'user_id',
            'cat_id',
            'price',
            'quantity',
            'status',
            'created_at'
        ])
        ->with('category');

        // Apply filters conditionally
        if (!empty($searchName)) {
            $query->where('name', 'like', '%' . $searchName . '%');
        }

        if (!empty($searchCatId)) {
            $query->where('cat_id', $searchCatId);
        }

        if (isset($searchStatus)) {
            $query->where('status', $searchStatus);
        }

        if (!empty($searchCreatedAt)) {
            $query->whereDate('created_at', $searchCreatedAt);
        }

        // Add relationships
        $query->with('user', function ($query) {
            $query->select('userId', 'name');
        });

        $query->with('images', function ($query) {
            $query->select('product_id', 'image_path');
        });

        $query->with('category', function ($query) {
            $query->select('id', 'name');
        });

        $products = $query->orderBy('id', 'DESC')->paginate(PER_PAGE);

        return $products;
    }
}
