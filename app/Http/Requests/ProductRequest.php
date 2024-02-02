<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
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
            "name" => 'required|string|max:255',
            "price" => 'required|numeric',
            "image" => 'required|file|image',
            "quantity" => 'required|numeric',
            "describes" => 'required|string|max:2000',
            "user_id" => 'exists:App\Models\User,id',
            'user_id.exists' => 'Not an existing ID'
        ];
    }
}
