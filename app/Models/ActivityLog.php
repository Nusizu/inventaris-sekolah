<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public const ACTION_LABELS = [
        'create_category' => 'Tambah Kategori',
        'update_category' => 'Ubah Kategori',
        'delete_category' => 'Hapus Kategori',
        'create_item' => 'Tambah Barang',
        'update_item' => 'Ubah Barang',
        'delete_item' => 'Hapus Barang',
        'create_loan' => 'Tambah Peminjaman',
        'verify_loan' => 'Verifikasi Peminjaman',
        'return_loan' => 'Pengembalian',
    ];

    protected $fillable = ['user_id', 'action', 'description'];

    protected function actionLabel(): Attribute
    {
        return Attribute::get(
            fn () => self::ACTION_LABELS[$this->action]
                ?? ucwords(str_replace('_', ' ', $this->action))
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}