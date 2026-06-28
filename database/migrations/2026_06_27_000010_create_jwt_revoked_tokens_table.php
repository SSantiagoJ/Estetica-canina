<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jwt_revoked_tokens')) {
            return;
        }

        Schema::create('jwt_revoked_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('jti', 80)->unique();
            $table->unsignedBigInteger('id_usuario')->nullable()->index();
            $table->timestamp('expires_at')->index();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jwt_revoked_tokens');
    }
};
