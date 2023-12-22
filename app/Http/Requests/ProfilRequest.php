<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfilRequest extends FormRequest
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
            "firstname" => 'string|max:255',
            "lastname" => 'string|max:255',
            "image" => 'required|image|mimes:jpg,png,jpeg|max:4048',
            "user_id" => 'exists:App\Models\User,id',
            'user_id.exists' => 'Not an existing ID'
        ];
    }
}
