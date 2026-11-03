<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanAlert extends Notification
{
    public function __construct(public Loan $loan, public string $type)
    {
    }

    public function via(object $notifiable): array
    {
        // Pengajuan baru untuk Admin cukup di aplikasi, sisanya juga lewat email
        return $this->type === 'requested' ? ['database'] : ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title(),
            'message' => $this->body(),
            'url' => $this->path(),
            'loan_id' => $this->loan->id,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title() . ' - ' . config('app.name'))
            ->view('emails.loan-alert', [
                'title' => $this->title(),
                'name' => $notifiable->name,
                'body' => $this->body(),
                'url' => url($this->path()),
                'actionText' => 'Buka Peminjaman Saya',
            ]);
    }

    private function title(): string
    {
        return match ($this->type) {
            'requested' => 'Pengajuan Peminjaman Baru',
            'approved' => 'Pengajuan Disetujui',
            'rejected' => 'Pengajuan Ditolak',
            'due_soon' => 'Pengingat Jatuh Tempo',
            'overdue' => 'Peminjaman Terlambat',
            default => 'Notifikasi Peminjaman',
        };
    }

    private function body(): string
    {
        $item = $this->loan->item->name;
        $qty = number_format($this->loan->quantity, 0, ',', '.');
        $due = $this->loan->due_date->translatedFormat('j M Y');

        return match ($this->type) {
            'requested' => "{$this->loan->user->name} mengajukan peminjaman {$item} sebanyak {$qty}.",
            'approved' => "Pengajuan peminjaman {$item} sebanyak {$qty} telah disetujui. Harap kembalikan sebelum {$due}.",
            'rejected' => "Pengajuan peminjaman {$item} sebanyak {$qty} ditolak. Alasan: " . ($this->loan->decision_note ?: '-'),
            'due_soon' => "Peminjaman {$item} sebanyak {$qty} jatuh tempo pada {$due}. Harap segera dikembalikan.",
            'overdue' => "Peminjaman {$item} sebanyak {$qty} telah melewati jatuh tempo ({$due}). Harap segera dikembalikan.",
            default => "Ada pembaruan pada peminjaman {$item}.",
        };
    }

    /** Alamat relatif yang dibuka saat notifikasi diklik. */
    private function path(): string
    {
        return $this->type === 'requested'
            ? route('admin.loans.index', ['status' => 'menunggu'], false)
            : route('user.loans.index', [], false);
    }
}