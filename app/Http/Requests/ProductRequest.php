<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'cat_id' => 'required|numeric',            
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'product_condition' => 'required|string',            
            'product_images' => 'nullable|array', 
            'product_images.*' => 'image|mimes:jpg,jpeg,png,gif|max:5120', 
    
        ];
    }

     /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 255 characters.',

            'cat_id.required' => 'Select a Category.',
            'cat_id.numeric' => 'Invalid Category.',

            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a valid string.',
            
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be at least 0.',

            'quantity.required' => 'The Quantity field is required.',
            'quantity.numeric' => 'The Quantity must be a valid number.',
            'quantity.min' => 'The Quantity must be at least 0.',

            'product_condition.required' => 'The Product Condition field is required.',
            'product_condition.string' => 'The Product Condition must be a valid string.',

            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status must be either 0 or 1.',


            'product_images.array' => 'The images field must be an array.',
            'product_images.*.image' => 'Each file must be a valid image.',
            'product_images.*.mimes' => 'Images must be of type: jpg, jpeg, png, or gif.',
            'product_images.*.max' => 'Each image must not exceed 2 MB in size.',
        ];
    }
}
