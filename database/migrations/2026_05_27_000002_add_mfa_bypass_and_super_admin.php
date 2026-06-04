<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'mfa_bypass')) {
                $table->boolean('mfa_bypass')->default(false)->after('mfa_verified_at');
            }
        });

        $now = now();
        $correo = 'admin@spa.com';

        $usuario = DB::table('usuarios')->where('correo', $correo)->first();

        if ($usuario) {
            DB::table('personas')
                ->where('id_persona', $usuario->id_persona)
                ->update([
                    'nombres' => 'Administrador',
                    'apellidos' => 'Principal',
                    'estado' => 'A',
                    'fecha_actualizacion' => $now,
                ]);

            DB::table('usuarios')
                ->where('id_usuario', $usuario->id_usuario)
                ->update([
                    'contrasena' => Hash::make('123456'),
                    'rol' => 'Admin',
                    'estado' => 'A',
                    'mfa_enabled' => true,
                    'mfa_verified_at' => $now,
                    'mfa_bypass' => true,
                    'fecha_actualizacion' => $now,
                ]);

            return;
        }

        $persona = DB::table('personas')->where('nro_documento', 'ADMIN001')->first();

        if (!$persona) {
            $idPersona = DB::table('personas')->insertGetId([
                'nombres' => 'Administrador',
                'apellidos' => 'Principal',
                'tipo_doc' => 'DNI',
                'nro_documento' => 'ADMIN001',
                'estado' => 'A',
                'fecha_creacion' => $now,
                'fecha_actualizacion' => $now,
            ]);
        } else {
            $idPersona = $persona->id_persona;
        }

        DB::table('usuarios')->insert([
            'id_persona' => $idPersona,
            'correo' => $correo,
            'contrasena' => Hash::make('123456'),
            'rol' => 'Admin',
            'estado' => 'A',
            'mfa_enabled' => true,
            'mfa_verified_at' => $now,
            'mfa_bypass' => true,
            'fecha_creacion' => $now,
            'fecha_actualizacion' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('usuarios')
            ->where('correo', 'admin@spa.com')
            ->update([
                'mfa_bypass' => false,
                'fecha_actualizacion' => now(),
            ]);

        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'mfa_bypass')) {
                $table->dropColumn('mfa_bypass');
            }
        });
    }
};
