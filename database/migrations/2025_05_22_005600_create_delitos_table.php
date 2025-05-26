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
        Schema::create('delitos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tipo_delito_id');
            $table->datetime('fecha_ocurrencia');
            $table->string('latitud');
            $table->string('longitud');
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tipo_delito_id')->references('id')->on('tipos_delitos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delitos');
    }
};
