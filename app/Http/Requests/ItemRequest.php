<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware role:admin
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items', 'code')->ignore($this->route('item')),
            ],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'condition' => ['required', Rule::in(Item::CONDITIONS)],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'kategori',
            'name' => 'nama barang',
            'code' => 'kode barang',
            'stock' => 'stok',
            'condition' => 'kondisi',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'name.required' => 'Nama barang wajib diisi.',
            'name.max' => 'Nama barang maksimal 255 karakter.',
            'code.required' => 'Kode barang wajib diisi.',
            'code.max' => 'Kode barang maksimal 50 karakter.',
            'code.unique' => 'Kode barang sudah digunakan.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'stock.min' => 'Stok tidak boleh kurang dari 0.',
            'stock.max' => 'Stok maksimal 1.000.000.',
            'condition.required' => 'Kondisi wajib dipilih.',
            'condition.in' => 'Kondisi yang dipilih tidak valid.',
        ];
    }
}