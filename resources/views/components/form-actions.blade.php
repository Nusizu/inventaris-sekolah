{{--
    Tombol bawah form: Batal + Simpan. Di HP bertumpuk (Simpan di atas), di layar lebar rata kanan.
    Pemakaian: <x-form-actions :cancel="route('admin.categories.index')" />
               <x-form-actions :cancel="..." label="Simpan Pengembalian" />
--}}
@props(['cancel', 'label' => 'Simpan'])

<div class="mt-8 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ $cancel }}" class="btn btn-secondary">Batal</a>
    <x-primary-button>{{ $label }}</x-primary-button>
</div>