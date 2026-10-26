<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware role
    }

    public function rules(): array
    {
        $rules = [
            'item_id' => ['required', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
            'loan_date' => ['required', 'date', 'after_or_equal:today'],
            'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
        ];

        if ($this->user()->isAdmin()) {
            $rules['user_id'] = ['required', Rule::exists('users', 'id')->where('role', 'user')];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'peminjam',
            'item_id' => 'barang',
            'quantity' => 'jumlah',
            'loan_date' => 'tanggal pinjam',
            'due_date' => 'tanggal jatuh tempo',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Peminjam wajib dipilih.',
            'user_id.exists' => 'Peminjam yang dipilih tidak valid.',
            'item_id.required' => 'Barang wajib dipilih.',
            'item_id.exists' => 'Barang yang dipilih tidak valid.',
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah minimal 1.',
            'loan_date.required' => 'Tanggal pinjam wajib diisi.',
            'loan_date.date' => 'Tanggal pinjam tidak valid.',
            'loan_date.after_or_equal' => 'Tanggal pinjam tidak boleh sebelum hari ini.',
            'due_date.required' => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.date' => 'Tanggal jatuh tempo tidak valid.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo tidak boleh sebelum tanggal pinjam.',
        ];
    }
}