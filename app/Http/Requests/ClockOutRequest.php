<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClockOutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'clock_in_timestamp' => 'required|date',
            // Anda dapat menambahkan aturan validasi lainnya sesuai kebutuhan
        ];
    }
}
