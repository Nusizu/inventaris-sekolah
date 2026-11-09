<div class="card p-6">
    <h3 class="text-base font-semibold text-gray-900">Pindai QR Code Barang</h3>
    <p class="mt-1 text-sm text-gray-500">
        Arahkan kamera ke QR Code pada label barang, atau ketik atau pindai kode barang di kolom bawah.
    </p>

    {{--
        PENTING: id "qr-reader", "qr-status", "qr-start", "qr-stop" dan atribut data-scan-url
        dipakai oleh resources/js/qr-scanner.js. Jangan diubah.
        Kotak putus-putus di bawah tampil selama #qr-reader masih kosong (kamera belum aktif)
        dan otomatis hilang begitu kamera menambahkan elemen video ke dalamnya.
    --}}
    <div class="mx-auto mt-5 w-full max-w-sm">
        <div id="qr-reader" data-scan-url="{{ $scanUrl }}" class="peer overflow-hidden rounded-lg bg-gray-100"></div>

        <div class="flex aspect-square flex-col items-center justify-center gap-3 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 text-center peer-[:not(:empty)]:hidden">
            <x-icon name="qr-code" class="h-12 w-12 text-gray-300" />
            <p class="px-6 text-sm text-gray-500">Tekan &ldquo;Mulai Kamera&rdquo; untuk mulai memindai.</p>
        </div>
    </div>

    <p id="qr-status" class="mt-4 text-center text-sm text-gray-600">Kamera belum aktif.</p>

    <div class="mt-3 flex justify-center gap-2">
        <button type="button" id="qr-start" class="btn btn-primary w-full sm:w-auto">
            Mulai Kamera
        </button>
        <button type="button" id="qr-stop" class="btn btn-secondary hidden w-full sm:w-auto">
            Hentikan Kamera
        </button>
    </div>

    <form method="GET" action="{{ $scanUrl }}" class="mt-6 border-t border-gray-100 pt-6">
        <x-input-label for="code">
            Kode Barang <span class="text-red-600">*</span>
        </x-input-label>
        <div class="mt-1 flex gap-2">
            <x-text-input id="code" name="code" type="text" class="block w-full font-mono"
                :value="$code" placeholder="Contoh: LAB-001" required autofocus />
            <x-primary-button class="shrink-0">Cari</x-primary-button>
        </div>
    </form>
</div>