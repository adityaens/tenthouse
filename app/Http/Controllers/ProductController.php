<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Http\Requests\ProductRequest;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $debugMode;

    public function __construct()
    {
        $this->debugMode = config('constants.debug_mode');    
    }

    public function index()
    {
        $products = Product::select([
            'id',
            'name',
            'user_id',
            'cat_id',
            'price',
            'quantity',
            'status',
            'created_at'
        ])
        ->with('user', function($query) {
            $query->select('userId', 'name');
        })
        ->with('images', function($query) {
            $query->select('product_id', 'image_path');
        })
        // ->with('category', function($query) {
        //     $query->select('name');
        // })
        ->orderBy('id', 'DESC')
        ->paginate(PER_PAGE);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product in storage.
     * 
     * @param \App\Http\Requests\ProductRequest $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductRequest $request, Product $product)
    {
        $isSaved = false;

        try {
            $product->name = $request->input('name');
            $product->user_id = auth()->user()->id;
            $product->cat_id = $request->input('cat_id');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->quantity = $request->input('quantity');
            $product->status = $request->input('status');
            $product->product_condition = $request->input('product_condition');

            $isSaved = $product->save();

            if (!$isSaved) {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Product not saved.'));
            }

            $productImages = $request->file('product_images');
            if (is_array($productImages) && count($productImages) > 0) {
                $uploadedFiles = uploadMultipleFiles($productImages); 
                if (!$uploadedFiles) {
                    return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Images not uploaded.'));
                }

                foreach ($uploadedFiles as $filePath) {
                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id; 
                    $productImage->image_path = $filePath;
                    $productImage->save();
                }
            } else {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'No images found'));
            }

            return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', showErrorMessage($this->debugMode, $e->getMessage()));
        }
    }


}
