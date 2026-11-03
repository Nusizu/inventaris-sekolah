<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Mencegah pengingat yang sama terkirim berulang kali
            $table->timestamp('due_reminder_sent_at')->nullable()->after('decision_note');
            $table->timestamp('overdue_notice_sent_at')->nullable()->after('due_reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['due_reminder_sent_at', 'overdue_notice_sent_at']);
        });
    }
};