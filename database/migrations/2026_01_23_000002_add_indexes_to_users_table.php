<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function columnHasAnyIndex(string $table, string $column): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $dbName = DB::getDatabaseName();
            $rows = DB::select(
                'SELECT 1
                 FROM information_schema.statistics
                 WHERE table_schema = ?
                   AND table_name = ?
                   AND column_name = ?
                 LIMIT 1',
                [$dbName, $table, $column]
            );
            return ! empty($rows);
        }

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$table}')");
            foreach ($indexes as $idx) {
                $indexName = $idx->name ?? null;
                if (! $indexName) {
                    continue;
                }
                $cols = DB::select("PRAGMA index_info('{$indexName}')");
                foreach ($cols as $c) {
                    if (($c->name ?? null) === $column) {
                        return true;
                    }
                }
            }
            return false;
        }

        // Fallback: don't block migration on unknown drivers; attempt create.
        return false;
    }

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role') && ! $this->columnHasAnyIndex('users', 'role')) {
                $table->index('role');
            }
            if (Schema::hasColumn('users', 'plan') && ! $this->columnHasAnyIndex('users', 'plan')) {
                $table->index('plan');
            }
            if (Schema::hasColumn('users', 'email') && ! $this->columnHasAnyIndex('users', 'email')) {
                $table->index('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Best-effort cleanup (some installs may already have these via unique/index elsewhere).
            try { $table->dropIndex(['role']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['plan']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['email']); } catch (\Throwable $e) {}
        });
    }
};

