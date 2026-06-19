<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos', 'gateway')) {
                $table->string('gateway', 40)->nullable()->after('metodo_pago');
            }

            if (!Schema::hasColumn('pagos', 'provider_payment_id')) {
                $table->string('provider_payment_id', 100)->nullable()->after('gateway');
            }

            if (!Schema::hasColumn('pagos', 'estado_gateway')) {
                $table->string('estado_gateway', 40)->nullable()->after('provider_payment_id');
            }

            if (!Schema::hasColumn('pagos', 'fecha_confirmacion')) {
                $table->dateTime('fecha_confirmacion')->nullable()->after('estado_gateway');
            }

            if (!Schema::hasColumn('pagos', 'monto_neto')) {
                $table->decimal('monto_neto', 10, 2)->nullable()->after('monto');
            }

            if (!Schema::hasColumn('pagos', 'codigo_operacion')) {
                $table->string('codigo_operacion', 80)->nullable()->after('series');
            }

            if (!Schema::hasColumn('pagos', 'comprobante_path')) {
                $table->string('comprobante_path')->nullable()->after('codigo_operacion');
            }
        });

        if (!Schema::hasTable('pago_notificaciones')) {
            Schema::create('pago_notificaciones', function (Blueprint $table) {
                $table->id('id_pago_notificacion');
                $table->unsignedInteger('id_pago');
                $table->unsignedInteger('id_usuario')->nullable();
                $table->string('rol_destino', 40)->nullable();
                $table->string('canal', 30)->default('sistema');
                $table->string('titulo', 160);
                $table->text('mensaje');
                $table->char('estado', 1)->default('P');
                $table->dateTime('fecha_envio')->nullable();
                $table->string('usuario_creacion', 100)->nullable();
                $table->string('usuario_actualizacion', 100)->nullable();
                $table->timestamps();

                $table->index(['id_pago', 'rol_destino']);
                $table->index(['id_usuario', 'estado']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_notificaciones');

        Schema::table('pagos', function (Blueprint $table) {
            foreach ([
                'comprobante_path',
                'codigo_operacion',
                'monto_neto',
                'fecha_confirmacion',
                'estado_gateway',
                'provider_payment_id',
                'gateway',
            ] as $column) {
                if (Schema::hasColumn('pagos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
