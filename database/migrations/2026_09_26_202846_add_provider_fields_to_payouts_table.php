<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payouts', function (Blueprint $table): void {
            $table->string('idempotency_key')->nullable()->after('id');
            $table->string('provider_reference')->nullable()->after('processed_by_manager_id');
            $table->string('provider_status')->nullable()->after('provider_reference');
            $table->text('failure_reason')->nullable()->after('provider_status');
            $table->timestamp('last_provider_checked_at')->nullable()->after('failure_reason');
        });

        // Backfill stable keys for existing rows before unique index.
        foreach (DB::table('payouts')->pluck('id') as $id) {
            DB::table('payouts')->where('id', $id)->update([
                'idempotency_key' => 'payout-'.$id,
            ]);
        }

        Schema::table('payouts', function (Blueprint $table): void {
            $table->unique('idempotency_key');
            $table->unique('provider_reference');
        });

        Schema::table('teacher_ledger_entries', function (Blueprint $table): void {
            $table->unique(
                ['teacher_id', 'reference_type', 'reference_id', 'type'],
                'teacher_ledger_teacher_reference_type_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('teacher_ledger_entries', function (Blueprint $table): void {
            $table->dropUnique('teacher_ledger_teacher_reference_type_unique');
        });

        Schema::table('payouts', function (Blueprint $table): void {
            $table->dropUnique(['idempotency_key']);
            $table->dropUnique(['provider_reference']);
            $table->dropColumn([
                'idempotency_key',
                'provider_reference',
                'provider_status',
                'failure_reason',
                'last_provider_checked_at',
            ]);
        });
    }
};
