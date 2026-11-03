<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

    public const STATUSES = ['menunggu', 'dipinjam', 'dikembalikan', 'terlambat', 'ditolak', 'dibatalkan'];

    /** Status ketika barang sedang di tangan peminjam (stok sudah dipotong). */
    public const ACTIVE_STATUSES = ['dipinjam', 'terlambat'];

    protected $fillable = [
        'user_id', 'item_id', 'quantity', 'loan_date', 'due_date', 'status',
        'decided_by', 'decided_at', 'decision_note',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'due_date' => 'date',
            'decided_at' => 'datetime',
        ];
    }

    public static function markOverdue(): int
    {
        return static::where('status', 'dipinjam')
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'terlambat']);
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function returnLog(): HasOne
    {
        return $this->hasOne(ReturnLog::class);
    }
}