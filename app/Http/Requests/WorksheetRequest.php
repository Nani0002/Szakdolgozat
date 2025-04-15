<?php

namespace App\Http\Requests;

use App\Models\Outsourcing;
use App\Models\Worksheet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorksheetRequest extends FormRequest
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
        $isOutsourced = $this->input('outsourcing') === true || $this->input('outsourcing') === '1';
        $worksheetId = $this->route('worksheet')?->id;

        $types = implode(',', array_keys(Worksheet::getTypes()));

        $rules = [
            'sheet_number' => [
                'required',
                Rule::unique('worksheets', 'sheet_number')->ignore($worksheetId),
            ],
            'sheet_type' => 'required|in:maintanance,paid,warranty',
            'current_step' => "required|in:$types",
            'declaration_mode' => 'required|in:email,phone,personal,onsite',
            'declaration_time' => 'required|date',
            'declaration_time_hour' => 'required|date_format:H:i',
            'print_date' => 'nullable|date',
            'print_date_hour' => 'nullable|date_format:H:i',
            'liable_id' => 'required|integer',
            'coworker_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'work_start' => 'required|date',
            'work_start_hour' => 'required|date_format:H:i',
            'work_end' => 'nullable|date',
            'work_end_hour' => 'nullable|date_format:H:i',
            'work_time' => 'nullable|integer',
            'comment' => 'nullable|string',
            'error_description' => 'required|string',
            'work_description' => 'nullable|string',
            'outsourcing' => 'required|boolean',
        ];

        if ($isOutsourced) {
            $outsourcing = $this->route('worksheet')?->outsourcing;
            $rules = array_merge($rules, [
                'partner_id' => 'required|integer',
                'entry_time' => 'required|date',
                'entry_time_hour' => 'required|date_format:H:i',
                'finished' => 'required|in:ongoing,finished,brought',
                'outsourced_price' => 'required|numeric',
                'our_price' => 'required|numeric',
                'outsourced_number' => [
                    'required',
                    Rule::unique('outsourcings', 'outsourced_number')
                        ->ignore($outsourcing instanceof Outsourcing ? $outsourcing->id : null),
                ],
            ]);
        }

        return $rules;
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        $messages = [
            'sheet_number.required' => 'A munkalapszám megadása kötelező.',
            'sheet_number.unique' => 'Ez a munkalapszám már létezik.',
            'sheet_type.required' => 'Munkalap típus megadása kötelező.',
            'sheet_type.in' => 'A megadott munkalap típus érvénytelen.',
            'current_step.required' => 'A munkalap állapotának megadása kötelező.',
            'current_step.in' => 'A megadott állapot nem érvényes.',
            'declaration_mode.required' => 'Bejelentési mód megadása kötelező.',
            'declaration_mode.in' => 'A megadott bejelentési mód nem érvényes.',
            'declaration_time.required' => 'A bejelentés dátuma kötelező.',
            'declaration_time.date' => 'A bejelentés dátuma nem érvényes.',
            'declaration_time_hour.required' => 'A bejelentés időpontja kötelező.',
            'declaration_time_hour.date_format' => 'Az bejelentés időpontja nem megfelelő (HH:MM).',
            'print_date.date' => 'A nyomtatás dátuma nem érvényes.',
            'print_date_hour.date_format' => 'A nyomtatás időpontja nem megfelelő (HH:MM).',
            'liable_id.required' => 'Felelős megadása kötelező.',
            'liable_id.integer' => 'A felelős azonosítója nem megfelelő.',
            'coworker_id.required' => 'Munkatárs megadása kötelező.',
            'coworker_id.integer' => 'A munkatárs azonosítója nem megfelelő.',
            'customer_id.required' => 'Ügyfél megadása kötelező.',
            'customer_id.integer' => 'Az ügyfél azonosítója nem megfelelő.',
            'work_start.required' => 'A kezdés dátuma megadása kötelező.',
            'work_start.date' => 'A kezdés dátuma nem megfelelő.',
            'work_start_hour.required' => 'A kezdés időpontja megadása kötelező.',
            'work_start_hour.date_format' => 'A kezdés időpontja nem megfelelő (HH:MM).',
            'work_end.date' => 'A befejezés dátuma nem megfelelő.',
            'work_end_hour.date_format' => 'A befejezés időpontja nem megfelelő (HH:MM).',
            'work_time.integer' => 'A munkaidőnek egész számnak kell lennie (30 perces egységekben).',
            'comment.string' => 'A hozzáadott komment formátuma nem megfelelő.',
            'error_description.required' => 'A hibaleírás megadása kötelező.',
            'error_description.string' => 'A hibaleírás formátuma nem megfelelő.',
            'work_description.string' => 'A hibaleírás formátuma nem megfelelő.',
            'outsourcing.required' => 'Kérlek jelezd, hogy a munkalap külsős-e.',
            'outsourcing.boolean' => 'A külsős értéke igaz vagy hamis kell legyen.',
        ];

        $isOutsourced = $this->input('outsourcing') === true || $this->input('outsourcing') === '1';

        if ($isOutsourced) {
            $messages = array_merge($messages, [
                'partner_id.required' => 'Kérlek válassz partner céget.',
                'partner_id.integer' => 'A partner azonosítója nem megfelelő.',
                'entry_time.required' => 'Kérlek add meg a beviteli dátumot.',
                'entry_time.date' => 'A beviteli dátum nem megfelelő.',
                'entry_time_hour.required' => 'A beviteli időpont megadása kötelező.',
                'entry_time_hour.date_format' => 'A beviteli időpont formátuma nem megfelelő (HH:MM).',
                'finished.required' => 'Kérlek válassz külsős státuszt.',
                'finished.in' => 'A külsős státusz érvénytelen.',
                'outsourced_price.required' => 'Kérlek add meg a partner árát.',
                'outsourced_price.numeric' => 'A partner árának számnak kell lennie.',
                'our_price.required' => 'Kérlek add meg a saját árat.',
                'our_price.numeric' => 'A saját árnak számnak kell lennie.',
                'outsourced_number.required' => 'A külsős munkalapszám megadása kötelező.',
                'outsourced_number.unique' => 'Ez a külsős munkalapszám már létezik.',
            ]);
        }

        return $messages;
    }
}
