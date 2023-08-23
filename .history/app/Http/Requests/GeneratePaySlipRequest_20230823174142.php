<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneratePaySlipRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'month' => 'required|date_format:Y-m',
            'employee_id' => 'required|exists:employees,id',
            // Anda dapat menambahkan aturan validasi lainnya sesuai kebutuhan
        ];
    }
}

