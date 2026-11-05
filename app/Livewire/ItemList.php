<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Item;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ItemList extends Component
{
    use WithPagination;

    /** 'admin' atau 'user'. Dikunci agar tidak bisa diubah dari browser. */
    #[Locked]
    public string $scope = 'user';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'category_id', except: '')]
    public string $categoryId = '';

    #[Url(as: 'condition', except: '')]
    public string $condition = '';

    #[Url(as: 'availability', except: '')]
    public string $availability = '';

    public function mount(string $scope = 'user'): void
    {
        $this->scope = $scope === 'admin' ? 'admin' : 'user';

        $this->authorizeScope();
    }

    /** Setiap filter berubah, kembali ke halaman pertama. */
    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'categoryId', 'condition', 'availability'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryId', 'condition', 'availability']);
        $this->resetPage();
    }

    public function render()
    {
        // Diperiksa di setiap permintaan, bukan hanya saat halaman pertama dibuka
        $this->authorizeScope();

        $search = trim($this->search);

        $items = Item::with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(
                ctype_digit($this->categoryId),
                fn ($query) => $query->where('category_id', (int) $this->categoryId)
            )
            ->when(
                $this->scope === 'admin' && in_array($this->condition, Item::CONDITIONS, true),
                fn ($query) => $query->where('condition', $this->condition)
            )
            ->when(
                $this->scope === 'user' && $this->availability === 'tersedia',
                fn ($query) => $query->available()
            )
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.item-list-' . $this->scope, [
            'items' => $items,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function authorizeScope(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === $this->scope, 403);
    }
}