<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware role:admin
    }

    public function rules(): array
    {
        return [
            'decision_note' => ['required', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return ['decision_note' => 'alasan penolakan'];
    }

    public function messages(): array
    {
        return [
            'decision_note.required' => 'Alasan penolakan wajib diisi.',
            'decision_note.max' => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }
}