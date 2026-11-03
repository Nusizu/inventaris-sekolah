<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE loans MODIFY status "
            . "ENUM('menunggu','dipinjam','dikembalikan','terlambat','ditolak','dibatalkan') "
            . "NOT NULL DEFAULT 'dipinjam'"
        );

        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('decided_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable()->after('decided_by');
            $table->text('decision_note')->nullable()->after('decided_at');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('decided_by');
            $table->dropColumn(['decided_at', 'decision_note']);
        });

        DB::table('loans')
            ->whereIn('status', ['menunggu', 'ditolak', 'dibatalkan'])
            ->update(['status' => 'dikembalikan']);

        DB::statement(
            "ALTER TABLE loans MODIFY status "
            . "ENUM('dipinjam','dikembalikan','terlambat') NOT NULL DEFAULT 'dipinjam'"
        );
    }
};