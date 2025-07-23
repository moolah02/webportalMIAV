<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportTerminalsRequest extends FormRequest
{
    public function authorize() { return auth()->check(); }

    public function rules()
    {
        return [
            'client_id'   => ['required','exists:clients,id'],
            'data_file'   => ['required','file','mimes:csv,xlsx,xls'],
        ];
    }
}
