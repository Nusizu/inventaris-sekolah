<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\User;
use App\Notifications\LoanAlert;
use Illuminate\Support\Facades\Notification;
use Throwable;

class LoanNotifier
{
    /** Pengajuan baru: semua Admin diberi tahu. */
    public static function requested(Loan $loan): void
    {
        self::send(User::where('role', 'admin')->get(), new LoanAlert($loan, 'requested'));
    }

    public static function approved(Loan $loan): void
    {
        self::send($loan->user, new LoanAlert($loan, 'approved'));
    }

    public static function rejected(Loan $loan): void
    {
        self::send($loan->user, new LoanAlert($loan, 'rejected'));
    }

    public static function dueSoon(Loan $loan): void
    {
        self::send($loan->user, new LoanAlert($loan, 'due_soon'));
    }

    public static function overdue(Loan $loan): void
    {
        self::send($loan->user, new LoanAlert($loan, 'overdue'));
    }

    /**
     * Kegagalan notifikasi (misalnya email) dicatat ke log,
     * tetapi tidak boleh membatalkan aksi utama.
     */
    private static function send($notifiable, LoanAlert $notification): void
    {
        try {
            Notification::send($notifiable, $notification);
        } catch (Throwable $e) {
            report($e);
        }
    }
}