<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'mfa_enabled')) {
                $table->boolean('mfa_enabled')->default(false)->after('estado');
            }

            if (!Schema::hasColumn('usuarios', 'mfa_verified_at')) {
                $table->timestamp('mfa_verified_at')->nullable()->after('mfa_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'mfa_verified_at')) {
                $table->dropColumn('mfa_verified_at');
            }

            if (Schema::hasColumn('usuarios', 'mfa_enabled')) {
                $table->dropColumn('mfa_enabled');
            }
        });
    }
};
