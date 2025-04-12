<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtraRequest extends FormRequest
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
        $extraId = $this->route('extra')?->id;
        $rules = [
            "worksheet_id"   => "required|exists:worksheets,id",
            "computer_id"    => "required|exists:computers,id",
            "manufacturer"   => "required|string",
            "type"           => "required|string",
        ];

        if (!$extraId) {
            $rules["serial_number"] = "required|string|unique:extras,serial_number";
        }

        return $rules;
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        $extraId = $this->route('extra')?->id;
        $messages = [
            "worksheet_id.required"  => "A munkalap azonosító megadása kötelező.",
            "worksheet_id.exists"    => "A megadott munkalap nem létezik.",
            "computer_id.required"   => "A számítógép azonosító megadása kötelező.",
            "computer_id.exists"     => "A megadott számítógép nem található.",
            "manufacturer.required"  => "A gyártó megadása kötelező.",
            "manufacturer.string"    => "A gyártó formátuma nem megfelelő.",
            "type.required"          => "A típus megadása kötelező.",
            "type.string"            => "A típus formátuma nem megfelelő.",
        ];

        if (!$extraId) {
            $messages = array_merge($messages, [
                "serial_number.required" => "A sorozatszám megadása kötelező.",
                "serial_number.string"   => "A sorozatszám formátuma nem megfelelő.",
                "serial_number.unique"   => "Ez a sorozatszám már szerepel egy másik extránál."
            ]);
        }

        return $messages;
    }
}
