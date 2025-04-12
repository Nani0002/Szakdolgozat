<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComputerRequest extends FormRequest
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
        $computerId = $this->route('computer')?->id;
        $rules = [
            "manufacturer" => "required|required",
            "type" => "required|string",
        ];

        if (!$computerId) {
            $rules["serial_number"] = "required|string|unique:computers,serial_number";
        }

        return $rules;
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        $computerId = $this->route('computer')?->id;
        $messages = [
            "manufacturer.required" => "A gyártó megadása kötelező.",
            "manufacturer.string" => "A gyártó formátuma nem megfelelő.",
            "type.required" => "A típus megadása kötelező.",
            "type.string" => "A típus formátuma nem megfelelő.",
        ];

        if (!$computerId) {
            $messages = array_merge($messages, [
                "serial_number.required" => "A sorozatszám megadása kötelező.",
                "serial_number.string"   => "A sorozatszám formátuma nem megfelelő.",
                "serial_number.unique" => "Ez a sorozatszám már szerepel az adatbázisban."
            ]);
        }

        return $messages;
    }
}
