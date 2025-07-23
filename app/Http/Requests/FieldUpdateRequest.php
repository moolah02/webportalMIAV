<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FieldUpdateRequest extends FormRequest
{
    public function authorize() { return auth()->check() && auth()->user()->role === 'technician'; }

    public function rules()
    {
        return [
            'terminal_id'  => ['required','exists:pos_terminals,id'],
            'service_type' => ['required','string'],
            'status'       => ['required','in:active,offline,maintenance,faulty,decommissioned'],
            'visit_at'     => ['required','date'],
            'next_due'     => ['nullable','date','after:visit_at'],
            'notes'        => ['nullable','string'],
            'issues_found' => ['nullable','array'],
            'issues_found.*' => ['in:Network,Card Reader,Printer,Software,Hardware,Power,Training'],
        ];
    }
}
