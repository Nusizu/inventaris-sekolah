{{--
    Paginasi untuk komponen Livewire (ItemList). Tampilannya sama dengan pagination/app,
    tetapi tombolnya memakai wire:click (tanpa muat ulang halaman).
    Dipilih lewat method paginationView() di app/Livewire/ItemList.php.
--}}
<div>
    @if ($paginator->hasPages())
        @php
            $pageName = $paginator->getPageName();
            // Setelah pindah halaman, gulir ke atas supaya daftar baru langsung terlihat
            $scrollTop = "window.scrollTo({ top: 0, behavior: 'smooth' })";
        @endphp

        <nav role="navigation" aria-label="Navigasi halaman"
             class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
            <p class="text-sm text-gray-500">
                Menampilkan
                <span class="font-medium text-gray-900">{{ number_format($paginator->firstItem(), 0, ',', '.') }}</span>&ndash;<span class="font-medium text-gray-900">{{ number_format($paginator->lastItem(), 0, ',', '.') }}</span>
                dari
                <span class="font-medium text-gray-900">{{ number_format($paginator->total(), 0, ',', '.') }}</span>
                data
            </p>

            <div class="flex items-center gap-1">
                {{-- Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Halaman sebelumnya"
                          class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-md border border-gray-200 text-gray-300">
                        <x-icon name="chevron-right" class="h-4 w-4 rotate-180" />
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $pageName }}')" x-on:click="{{ $scrollTop }}"
                            wire:loading.attr="disabled" rel="prev" aria-label="Halaman sebelumnya"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900">
                        <x-icon name="chevron-right" class="h-4 w-4 rotate-180" />
                    </button>
                @endif

                {{-- Nomor halaman (layar >= sm) --}}
                <div class="hidden items-center gap-1 sm:flex">
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="inline-flex h-9 min-w-[2.25rem] items-center justify-center px-1 text-sm text-gray-400">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <span wire:key="paginator-{{ $pageName }}-page-{{ $page }}">
                                    @if ($page == $paginator->currentPage())
                                        <span aria-current="page"
                                              class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded-md bg-gray-800 px-2 text-sm font-medium text-white">{{ $page }}</span>
                                    @else
                                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $pageName }}')" x-on:click="{{ $scrollTop }}"
                                                aria-label="Ke halaman {{ $page }}"
                                                class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded-md px-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900">{{ $page }}</button>
                                    @endif
                                </span>
                            @endforeach
                        @endif
                    @endforeach
                </div>

                {{-- Penanda halaman (HP) --}}
                <span class="px-3 text-sm text-gray-600 sm:hidden">
                    Hal. <span class="font-medium text-gray-900">{{ $paginator->currentPage() }}</span> dari {{ $paginator->lastPage() }}
                </span>

                {{-- Berikutnya --}}
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $pageName }}')" x-on:click="{{ $scrollTop }}"
                            wire:loading.attr="disabled" rel="next" aria-label="Halaman berikutnya"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-600 transition-colors hover:bg-gray-50 hover:text-gray-900">
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </button>
                @else
                    <span aria-disabled="true" aria-label="Halaman berikutnya"
                          class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-md border border-gray-200 text-gray-300">
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>