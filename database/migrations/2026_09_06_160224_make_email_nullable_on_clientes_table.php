<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite que clientes criados automaticamente durante
     * a análise de crédito não possuam e-mail.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('email')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverte o campo para obrigatório.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('email')
                ->nullable(false)
                ->change();
        });
    }
};