<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReturnLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware role:admin
    }

    public function rules(): array
    {
        $loan = $this->route('loan');

        return [
            'returned_at' => [
                'required',
                'date',
                'after_or_equal:' . $loan->loan_date->toDateString(),
                'before_or_equal:today',
            ],
            'condition_on_return' => ['required', Rule::in(Item::CONDITIONS)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'returned_at' => 'tanggal kembali',
            'condition_on_return' => 'kondisi barang',
            'notes' => 'catatan',
        ];
    }

    public function messages(): array
    {
        return [
            'returned_at.required' => 'Tanggal kembali wajib diisi.',
            'returned_at.date' => 'Tanggal kembali tidak valid.',
            'returned_at.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'returned_at.before_or_equal' => 'Tanggal kembali tidak boleh melebihi hari ini.',
            'condition_on_return.required' => 'Kondisi barang wajib dipilih.',
            'condition_on_return.in' => 'Kondisi barang yang dipilih tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}