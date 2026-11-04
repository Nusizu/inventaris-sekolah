<?php

namespace App\Http\Requests;

use App\Models\Loan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware role:admin
    }

    public function rules(): array
    {
        $dateTo = ['nullable', 'date_format:Y-m-d'];

        // Pembanding hanya dipakai bila tanggal awal diisi
        if ($this->filled('date_from')) {
            $dateTo[] = 'after_or_equal:date_from';
        }

        return [
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => $dateTo,
            'status' => ['nullable', Rule::in(Loan::STATUSES)],
        ];
    }

    public function attributes(): array
    {
        return [
            'date_from' => 'tanggal awal',
            'date_to' => 'tanggal akhir',
            'status' => 'status',
        ];
    }

    public function messages(): array
    {
        return [
            'date_from.date_format' => 'Format tanggal awal tidak valid.',
            'date_to.date_format' => 'Format tanggal akhir tidak valid.',
            'date_to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }
}