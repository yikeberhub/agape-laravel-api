<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('size')->nullable();
            $table->text('cause_of_need')->nullable();

            $table->foreignId('type_id')
                  ->constrained('equipment_types')
                  ->onDelete('set null');

            $table->foreignId('sub_type_id')
                  ->nullable()
                  ->constrained('equipment_sub_types')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
}

