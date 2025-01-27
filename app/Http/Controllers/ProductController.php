<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Http\Requests\ProductRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    private $debugMode;

    public function __construct()
    {
        $this->debugMode = config('constants.debug_mode');    
    }

    public function index(Request $request)
    {
        $searchName = $request->get('name', NULL);
        $searchCatId = $request->get('cat_id', NULL);
        $searchStatus = $request->get('status', NULL);
        $searchCreatedAt = $request->get('created_at', NULL);

        $productCategories = ProductCategory::select([
            'id',
            'name'
        ])
        ->get();

        $query = Product::select([
            'id',
            'name',
            'user_id',
            'cat_id',
            'price',
            'quantity',
            'status',
            'created_at'
        ]);
        
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
        $query->with('user', function($query) {
            $query->select('userId', 'name');
        });
        
        $query->with('images', function($query) {
            $query->select('product_id', 'image_path');
        });
        
        $query->with('category', function($query) {
            $query->select('id', 'name');
        });
        
        $products = $query->orderBy('id', 'DESC')->paginate(PER_PAGE);

        return view('admin.products.index', [
            'products' => $products,
            'productCategories' => $productCategories
        ]);
    }

    public function create()
    {
        $productCategories = ProductCategory::select([
            'id',
            'name'
        ])
        ->where('cat_status', 1)
        ->get();

        return view('admin.products.create', [
            'productCategories' => $productCategories
        ]);
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


    public function edit($id)
    {
        if(!empty($id)) {
            $product = Product::find($id);

            $productCategories = ProductCategory::select([
                'id',
                'name'
            ])
            ->where('cat_status', 1)
            ->get();

            return view('admin.products.edit', [
                'product' => $product,
                'productCategories' => $productCategories
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
    public function update(ProductRequest $request, $id)
    {
        $isUpdated = false;

        if(empty($id)) {
            return redirect()->route('admin.products.index')->with('error', showErrorMessage($this->debugMode, 'Id not found'));
        }

        try {
            $product = Product::find($id);
            if($request->has('name')) {
                $product->name = $request->input('name');
            }
            if($request->has('cat_id')) {
                $product->cat_id = $request->input('cat_id');
            }
            if($request->has('description')) {
                $product->description = $request->input('description');
            }
            if($request->has('price')) {
                $product->price = $request->input('price');
            }
            if($request->has('quantity')) {  
                $product->quantity = $request->input('quantity');
            }
            if($request->has('status')) {
                $product->status = $request->input('status');
            }
            if($request->has('product_condition')) {
                $product->product_condition = $request->input('product_condition');
            }
            $isUpdated = $product->update();

            if (!$isUpdated) {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Product not updated.'));
            }

            $productImages = $request->file('product_images');
            if (is_array($productImages) && count($productImages) > 0) {
                $deleteOldImages = ProductImage::where('product_id', $id)->delete();

                // if (File::exists($filePath)) {
                //     File::delete($filePath);
                //     echo "File deleted successfully!";
                // } 

                if($deleteOldImages) {
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
                    return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Old images NOT deleted'));
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
