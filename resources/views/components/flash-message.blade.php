@if (session('success'))
    {{-- Pesan sukses hilang sendiri setelah 6 detik --}}
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)"
         x-show="show" x-transition.opacity.duration.300ms role="status"
         class="mb-5 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
        <p class="flex-1">{{ session('success') }}</p>
        <button type="button" @click="show = false" class="-m-1 rounded p-1 text-emerald-600 hover:bg-emerald-100" aria-label="Tutup pesan">
            <x-icon name="close" class="h-4 w-4" />
        </button>
    </div>
@endif

@if (session('error'))
    {{-- Pesan galat tetap tampil sampai ditutup --}}
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms role="alert"
         class="mb-5 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <x-icon name="alert-circle" class="mt-0.5 h-5 w-5 shrink-0 text-red-600" />
        <p class="flex-1">{{ session('error') }}</p>
        <button type="button" @click="show = false" class="-m-1 rounded p-1 text-red-600 hover:bg-red-100" aria-label="Tutup pesan">
            <x-icon name="close" class="h-4 w-4" />
        </button>
    </div>
@endif