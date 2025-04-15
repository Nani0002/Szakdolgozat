<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\Worksheet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DragAndDropRequest extends FormRequest
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
        $routeName = $this->route()->getName();

        if ($routeName === 'worksheet.move') {
            $statusList = array_keys(Worksheet::getTypes());
            $idRule = 'exists:worksheets,id';
        } elseif ($routeName === 'ticket.move') {
            $statusList = array_keys(Ticket::getStatuses());
            $idRule = 'exists:tickets,id';
        } else {
            $statusList = [];
            $idRule = 'required';
        }

        return [
            'id' => ['required', 'integer', $idRule],
            'newStatus' => ['required', Rule::in($statusList)],
            'newSlot' => ['required', 'integer', 'min:0'],
        ];
    }
}
