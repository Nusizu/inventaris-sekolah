<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Services\LoanNotifier;
use Illuminate\Console\Command;

class SendLoanReminders extends Command
{
    protected $signature = 'loans:send-reminders';

    protected $description = 'Kirim pengingat jatuh tempo dan pemberitahuan keterlambatan peminjaman';

    public function handle(): int
    {
        // Pastikan status terlambat sudah mutakhir sebelum memeriksa
        Loan::markOverdue();

        // Jatuh tempo hari ini atau besok, dan belum pernah diingatkan
        $dueSoon = Loan::with(['user', 'item'])
            ->where('status', 'dipinjam')
            ->whereNull('due_reminder_sent_at')
            ->whereDate('due_date', '>=', today())
            ->whereDate('due_date', '<=', today()->addDay())
            ->get();

        foreach ($dueSoon as $loan) {
            LoanNotifier::dueSoon($loan);
            Loan::whereKey($loan->id)->update(['due_reminder_sent_at' => now()]);
        }

        // Sudah terlambat dan belum pernah diberi tahu
        $overdue = Loan::with(['user', 'item'])
            ->where('status', 'terlambat')
            ->whereNull('overdue_notice_sent_at')
            ->get();

        foreach ($overdue as $loan) {
            LoanNotifier::overdue($loan);
            Loan::whereKey($loan->id)->update(['overdue_notice_sent_at' => now()]);
        }

        $this->info("Pengingat jatuh tempo: {$dueSoon->count()}, pemberitahuan terlambat: {$overdue->count()}.");

        return self::SUCCESS;
    }
}