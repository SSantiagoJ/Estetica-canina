<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('usuarios', 'rol')) {
            return;
        }

        DB::statement("ALTER TABLE usuarios MODIFY rol ENUM('Cliente','Empleado','Supervisor','Admin') NULL DEFAULT 'Cliente'");
    }

    public function down(): void
    {
        if (!Schema::hasColumn('usuarios', 'rol')) {
            return;
        }

        DB::table('usuarios')
            ->where('rol', 'Supervisor')
            ->update(['rol' => 'Empleado']);

        DB::statement("ALTER TABLE usuarios MODIFY rol ENUM('Cliente','Empleado','Admin') NULL DEFAULT 'Cliente'");
    }
};

