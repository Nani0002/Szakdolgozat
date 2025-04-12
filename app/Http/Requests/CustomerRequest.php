<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name"  => "required|string",
            "email" => "required|string|email",
            "phone" => "required|string",
        ];
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            "name.required"  => "A név megadása kötelező.",
            "name.string"    => "A név csak szöveg lehet.",
            "email.required" => "Az email megadása kötelező.",
            "email.string"   => "Az email formátuma nem megfelelő.",
            "email.email"    => "Az email cím nem érvényes.",
            "phone.required" => "A telefonszám megadása kötelező.",
            "phone.string"   => "A telefonszám csak szöveg lehet.",
        ];
    }
}
