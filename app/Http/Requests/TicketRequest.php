<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
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
            "title" => "required|string",
            "text" => "required|string",
            "status" => "required|string",
            "users" => "required|array"
        ];
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Munkajegy cím megadása kötelező.',
            'title.string' => 'A hozzáadott cím formátuma nem megfelelő.',
            'text.required' => 'Szöveg megadása kötelező.',
            'text.string' => 'A hozzáadott szöveg formátuma nem megfelelő.',
            'status.required' => 'A státusz állapot megadása kötelező.',
            'status.string' => 'A hozzáadott státusz formátuma nem megfelelő.',
            'users.required' => 'Legalább egy csatolt munkatárs megadása kötelező.',
            'users.array' => 'A hozzáadott felhasználók formátuma nem megfelelő.',
        ];
    }
}
