<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Dipakai Loan::markOverdue() dan filter status
            $table->index(['status', 'due_date'], 'loans_status_due_date_index');
            $table->index('loan_date', 'loans_loan_date_index');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->index('name', 'items_name_index');
            $table->index('condition', 'items_condition_index');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at', 'activity_logs_created_at_index');
            $table->index('action', 'activity_logs_action_index');
        });

        Schema::table('return_logs', function (Blueprint $table) {
            $table->index('returned_at', 'return_logs_returned_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'users_role_index');
            $table->index('name', 'users_name_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Menjaga nama kategori unik di level database, bukan hanya validasi
            $table->unique('name', 'categories_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex('loans_status_due_date_index');
            $table->dropIndex('loans_loan_date_index');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_name_index');
            $table->dropIndex('items_condition_index');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_created_at_index');
            $table->dropIndex('activity_logs_action_index');
        });

        Schema::table('return_logs', function (Blueprint $table) {
            $table->dropIndex('return_logs_returned_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_name_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_name_unique');
        });
    }
};