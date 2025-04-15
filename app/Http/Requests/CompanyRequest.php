<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
        $companyId = $this->route('company')?->id;
        $rules = [
            'name'      => 'required|string',
            'post_code' => 'required|string',
            'city'      => 'required|string',
            'street'    => 'required|string',
            'phone'     => 'required|string',
            'email'     => 'required|email',
        ];

        if (!$companyId) {
            $rules['type'] = 'required|string|in:customer,partner';
        }

        return $rules;
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        $companyId = $this->route('company')?->id;
        $messages = [
            'name.required'      => 'A cég neve kötelező.',
            'post_code.required' => 'Az irányítószám megadása kötelező.',
            'city.required'      => 'A város megadása kötelező.',
            'street.required'    => 'Az utca megadása kötelező.',
            'phone.required'     => 'A telefonszám megadása kötelező.',
            'email.required'     => 'Az email cím megadása kötelező.',
            'email.email'        => 'Az email formátuma nem megfelelő.',
        ];

        if (!$companyId) {
            $messages = array_merge($messages, [
                'type.required'      => 'A típus megadása kötelező.',
                'type.in'            => 'A megadott típus nem érvényes.',
            ]);
        }

        return $messages;
    }
}
