<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package_number')->unique()->nullable();
            $table->string('name', 255)->unique();
            $table->mediumText('html')->nullable();
            $table->boolean('packages_discount')->default(0);
            $table->mediumText('text')->nullable();
            $table->string('type')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('module_id')->nullable();
            $table->integer('client_qty')->nullable();
            $table->integer('taxable')->nullable();
            $table->integer('single_term')->nullable();
            $table->tinyInteger('prorata_day')->nullable();
            $table->tinyInteger('prorata_cuoff')->nullable();
            $table->tinyInteger('upgrades_use_renewal')->nullable();
            $table->enum('status', ['A', 'I'])->default('A');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /*

    El paquete tendra como atributos:
    Nombre - Obligatorio - Se debe validar que no sea repetido el nombre. - tipo input
    Descripcion -opcional. - añadir una caja de texto enriquecido. - text area
    Status - Obligatorio - un campo tipo select o combobox cuya opciones son
    Activo (A), Inactive (I), Restricted (R)
    Validaciones:
    A nivel de tabla los registros no seran borrados, en el campo status tendra un valor "T" que significa eliminado.
    Solo se puede listar y editar los paquetes cuyo status sea diferente a T.
    El nombre del paquete no puede ser repetido.
    */

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
