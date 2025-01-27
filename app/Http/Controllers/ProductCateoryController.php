<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCateoryController extends Controller
{

public function index(){
    $categories = ProductCategory::all();
    return view('admin.product_category.index', compact('categories'));
}

public function create(){
 return view('admin.product_category.create');
 }

 public function store(CategoryRequest $request)
 {   
   ProductCategory::create([
        'name' => $request->catName,
        'cat_status' => $request->catStatus
    ]);
    return view('admin.product_category.create');
    }
 
    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        return view('admin.product_category.edit', compact('category'));
    }

        // Update the category
        public function update(Request $request, $id)
        {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'cat_status' => 'required|boolean',
            ]);
    
            $category = ProductCategory::findOrFail($id);
            $category->update($data);
             return redirect()->route('admin.products.category.index')->with('success', 'Category updated successfully!');
        }

        public function destroy($id)
            {
            // Find the category by ID
            $category = ProductCategory::findOrFail($id);

            // Delete the category
            $category->delete();

            // Redirect back with a success message
            return redirect()->route('admin.products.category.index')->with('success', 'Category deleted successfully!');
            }
}
