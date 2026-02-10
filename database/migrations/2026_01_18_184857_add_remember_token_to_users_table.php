<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->rememberToken();
            });
        }
    }

    public function down(): void
    {
        // Intentionally left blank:
        // `remember_token` is already created in `0001_01_01_000000_create_users_table.php`.
        // Dropping it here could be destructive in environments where it already exists.
    }
};
