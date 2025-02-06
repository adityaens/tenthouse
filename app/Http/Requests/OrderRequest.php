<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        $rules = [
            'payment_method' => 'required|numeric',
            'booking_date_range' => 'required',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'due_date' => 'required',
            'status' => 'required|numeric',
            'delivered_by' => 'required|string',
        ];

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     * 
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'customer.required' => 'The customer field is required.',
            'customer.numeric' => 'The customer must be a valid number.',

            'product.required' => 'The product field is required.',
            'product.numeric' => 'The product must be a valid number.',

            'payment_method.required' => 'The payment method is required.',
            'payment_method.numeric' => 'The payment method must be a valid number.',

            'quantity.required' => 'The quantity field is required.',
            'quantity.numeric' => 'The quantity must be a valid number.',
            'quantity.min' => 'The quantity must be at least 0.',

            'booking_date_range.required' => 'The booking date range is required.',

            'total_amount.required' => 'The total amount field is required.',
            'total_amount.numeric' => 'The total amount must be a valid number.',
            'total_amount.min' => 'The total amount must be at least 0.',

            'paid_amount.required' => 'The paid amount field is required.',
            'paid_amount.numeric' => 'The paid amount must be a valid number.',
            'paid_amount.min' => 'The paid amount must be at least 0.',

            'due_date.required' => 'The due date field is required.',

            'status.required' => 'The status field is required.',
            'status.numeric' => 'The status must be a valid number.',

            'delivered_by.required' => 'The delivered by field is required.',
            'delivered_by.string' => 'The delivered by field must be a valid string.',
        ];
    }
}
