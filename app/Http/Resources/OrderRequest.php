<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "numOrder" => 'string',
            "priceTotal" => 'required|numeric',
            "quantity" => 'required|numeric',
            "product_id" => 'required|exists:App\Models\Product,id',
            "imageUrl" => 'required|string',
            "product_name" => 'required|string',
            "product_price" => 'required|numeric',
            'product_id.exists' => 'Not an existing ID'
        ];
    }
}
