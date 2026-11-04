<div class="bg-white p-6 shadow-sm sm:rounded-lg">
    <h3 class="text-sm font-semibold text-indigo-700">Pindai QR Code Barang</h3>
    <p class="mt-1 text-xs text-gray-500">
        Arahkan kamera ke QR Code pada label barang, atau ketik atau pindai kode barang di kolom bawah.
    </p>

    <div id="qr-reader" data-scan-url="{{ $scanUrl }}"
         class="mx-auto mt-4 w-full max-w-sm overflow-hidden rounded-md bg-gray-100"></div>

    <p id="qr-status" class="mt-3 text-center text-sm text-gray-600">Kamera belum aktif.</p>

    <div class="mt-3 flex justify-center gap-2">
        <button type="button" id="qr-start"
                class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
            Mulai Kamera
        </button>
        <button type="button" id="qr-stop"
                class="hidden items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50">
            Hentikan Kamera
        </button>
    </div>

    <form method="GET" action="{{ $scanUrl }}" class="mt-6 flex flex-col gap-2 sm:flex-row">
        <div class="flex-1">
            <x-input-label for="code">
                Kode Barang <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                :value="$code" placeholder="Contoh: LAB-001" required autofocus />
        </div>

        <div class="self-end">
            <x-primary-button>Cari</x-primary-button>
        </div>
    </form>
</div>