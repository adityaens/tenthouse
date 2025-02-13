<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Http\Requests\ProductRequest;
use App\Models\ProductLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;


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
            $product->user_id = auth()->user()->userId;
            $product->cat_id = $request->input('cat_id');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->quantity = $request->input('quantity');
            $product->used_qty = 0;
            $product->rem_qty = $request->input('quantity');
            $product->status = $request->input('status') ? $request->input('status') : 1;
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
            }

            if ($isSaved) {
                $this->product_logs($product->id, 'add', null, $product->toArray(), 'Product added successfully.');
            }
            return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', showErrorMessage($this->debugMode, $e->getMessage()));
        }
    }


    public function edit($id)
    {

        if (!empty($id)) {
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

        if (empty($id)) {
            return redirect()->route('admin.products.index')->with('error', showErrorMessage($this->debugMode, 'Id not found'));
        }

        try {
            $product = Product::find($id);
            if ($request->has('name')) {
                $product->name = $request->input('name');
            }
            if ($request->has('cat_id')) {
                $product->cat_id = $request->input('cat_id');
            }
            if ($request->has('description')) {
                $product->description = $request->input('description');
            }
            if ($request->has('price')) {
                $product->price = $request->input('price');
            }
            if ($request->has('quantity')) {
                $product->quantity = $request->input('quantity');
            }
            if ($request->has('status')) {
                $product->status = $request->input('status');
            }
            if ($request->has('product_condition')) {
                $product->product_condition = $request->input('product_condition');
            }

            $isUpdated = $product->update();

            if (!$isUpdated) {
                return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Product not updated.'));
            }

            $productImages = $request->file('product_images');
            if (is_array($productImages) && count($productImages) > 0) {
                // deleting existing records from the table and removing respective image
                $dbProductImages = ProductImage::where('product_id', $id)->get();

                if ($dbProductImages->isNotEmpty()) {

                    foreach ($dbProductImages as $image) {
                        $filePath = public_path($image->image_path);
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }


                $deleteOldImages = ProductImage::where('product_id', $id)->delete();

                // if($deleteOldImages) {
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
                // } 
                // else {
                //     return redirect()->back()->with('error', showErrorMessage($this->debugMode, 'Old images NOT deleted'));
                // }

            }
            //else {
            //     return redirect()->back()->with('error', showEr rorMessage($this->debugMode, 'No images found'));
            // }

            if ($isUpdated) {
                $oldValues = $product->getOriginal();
                $newValues = $product->getChanges();
                $this->product_logs($product->id, 'update', $oldValues, $newValues, 'Product updated successfully.');
            }

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', showErrorMessage($this->debugMode, $e->getMessage()));
        }
    }
    public function destroy($id)
    {

        $product = Product::find($id);
        if ($product) {
            $this->product_logs($product->id, 'delete', $product->toArray(), null, 'Product deleted successfully.');
        }

        if ($id) {
            $dbProductImages = ProductImage::where('product_id', $id)->get();
            if ($dbProductImages->isNotEmpty()) {

                foreach ($dbProductImages as $image) {
                    $filePath = public_path($image->image_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        $product = Product::find($id);
        if ($product) {
            $this->product_logs($product->id, 'delete', $product->toArray(), null, 'Product deleted successfully.');
            ProductImage::where('product_id', $id)->delete();
            $product->delete();
 
        }
            
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
        } else {
            return redirect()->route('admin.products.index')->with('error', 'Product not found');
        }
    }

    private function product_logs($productId, $changeType, $oldValues = null, $newValues = null, $remarks = null)
    {

        DB::table('product_logs')->insert([
            'product_id' => $productId,
            'user_id' => auth()->id(), // Get the authenticated user ID
            'change_type' => $changeType, // e.g., 'add', 'update', 'delete'
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'remarks' => $remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getProductsList(Request $request)
    {
        $productKeyword = $request->input('productKeyword');
        try {

            if(empty($productKeyword)) {
                return response()->json([
                    'success' => false,
                    'error' => showErrorMessage($this->debugMode, 'Nothing to search.')
                ]);
            }

            $products = Product::select([
                'id',
                'name',
                'rem_qty'
            ])
            ->where('status', ACTIVE)
            ->where('name', 'like', '%'. $productKeyword .'%')
            ->get();

            return response()->json([
                'success' => true,
                'products' => $products
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => showErrorMessage($this->debugMode, $e->getMessage())
            ]);
        }
    }

    /**
     * Import CSV File for Products
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $response = [];
        $response['status'] = 0;
        $httpStatusCode = 206;
        $insert = [];
        $error = [];

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:csv',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'error' => $validator->errors()->first()]);
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();

                $headers = ['NAME', 'SKU', 'CATEGORY', 'DESCRIPTION', 'PRICE', 'QUANTITY', 'PRODUCT_CONDITION', 'STATUS'];
                $csvHeaders = fgetcsv(fopen($file->getPathname(), 'r'));

                if ($csvHeaders !== $headers && array_intersect_assoc($csvHeaders, $headers) === $headers) {
                    return response()->json(['status' => 0, 'error' => 'Invalid CSV file.']);
                }
                $csvData = array_map('str_getcsv', file($file));

                $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); 
                $fileExtension = $file->getClientOriginalExtension();
                $uniqueId = time() . '_' . uniqid();
                $fileName = $originalFileName . '_' . $uniqueId . '.' . $fileExtension;
                $destinationPath = public_path('uploads/csv');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $fileName);

                foreach ($csvData as $index => $row) {
                    if ($index === 0) continue;

                    if (empty($row[0]) && empty($row[1]) && empty($row[2]) && empty($row[3]) && empty($row[4]) && !isset($row[5]) && !isset($row[6]) && !isset($row[7])) {
                        $error[] = $row;
                        continue;  
                    } 
                    
                    $category = ProductCategory::where('name', 'like', trim($row[2]))->first();
                    $catId = !empty($category->id) ? $category->id : '';

                    if(empty($catId)) {
                        $error[] = $row;
                        continue;
                    }

                    $insert['name'] = trim($row[0]);
                    $insert['sku'] = trim($row[1]) ?? '';
                    $insert['user_id'] = auth()->user()->userId;
                    $insert['cat_id'] = $catId;
                    $insert['description'] = trim($row[3]);
                    $insert['price'] = trim($row[4]);
                    $insert['quantity'] = trim($row[5]);
                    $insert['product_condition'] = trim($row[6]);
                    $insert['status'] = (trim($row[6]) == 'Active') ? 1 : 0;
                    
                    Product::updateOrCreate([
                        'sku' => trim($row[1])
                    ], $insert);
                }
                DB::commit();
                
                $response['status'] = 1;
                $response['message'] = 'Products imported successfully.';
                $response['file_details'] = ['name' => $fileName, 'extension' => $fileExtension];
                $httpStatusCode = 200;
            } else {
                DB::rollback();
                $response['error'] = 'No File to upload.';
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response['error'] = $e->getMessage();
        }
        return response()->json($response, $httpStatusCode);
    }
}
