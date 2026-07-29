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
        Schema::create('saprod', function (Blueprint $table) {
            $table->id();
            $table->string('codprod');
            $table->string('descrip');
            $table->string('descrip2')->nullable();
            $table->string('descrip3')->nullable();

            $table->text('observaciones')->nullable();

            $table->string('marca')->nullable();

            $table->string('refere')->nullable();
            $table->integer('codinst');
            $table->integer('esexento')->default(0)->nullable();
            $table->integer('exdecimal')->default(0)->nullable();
            $table->integer('cantxempaq')->default(0)->nullable();
            $table->double('volumen')->default(0)->nullable();
            $table->double('peso')->default(0)->nullable();
            $table->string('unidad')->nullable();

            $table->double('preciod')->default(0);
            $table->double('preciod2')->default(0)->nullable();
            $table->double('costod')->default(0)->nullable();
            $table->double('costod2')->default(0)->nullable();
            $table->double('costod3')->default(0);

            $table->integer('activo')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saprod');
    }
};
