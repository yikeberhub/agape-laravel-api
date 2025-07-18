<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentSubTypesTable extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_sub_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('equipment_type_id')
                  ->constrained('equipment_types')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_sub_types');
    }
}
