<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('usuarios', 'mfa_bypass')) {
            return;
        }

        $now = now();
        $correo = 'empleado@spa.com';
        $usuario = DB::table('usuarios')->where('correo', $correo)->first();

        if ($usuario) {
            DB::table('usuarios')
                ->where('id_usuario', $usuario->id_usuario)
                ->update([
                    'contrasena' => Hash::make('password'),
                    'estado' => 'A',
                    'mfa_enabled' => true,
                    'mfa_verified_at' => $now,
                    'mfa_bypass' => true,
                    'fecha_actualizacion' => $now,
                ]);

            return;
        }

        $persona = DB::table('personas')->where('nro_documento', 'EMPL001')->first();

        if (!$persona) {
            $idPersona = DB::table('personas')->insertGetId([
                'nombres' => 'Empleado',
                'apellidos' => 'Principal',
                'tipo_doc' => 'DNI',
                'nro_documento' => 'EMPL001',
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
            'contrasena' => Hash::make('password'),
            'rol' => 'Empleado',
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
        if (!Schema::hasColumn('usuarios', 'mfa_bypass')) {
            return;
        }

        DB::table('usuarios')
            ->where('correo', 'empleado@spa.com')
            ->update([
                'mfa_bypass' => false,
                'fecha_actualizacion' => now(),
            ]);
    }
};
