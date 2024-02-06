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
        Schema::create('mg_employee_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('tasks_id');

            // Menambah foreign key ke mg_project
            $table->foreign('tasks_id')->references('id')->on('mg_tasks')->onDelete('cascade');

            // Menambah foreign key ke mg_employee
            $table->foreign('employee_id')->references('id')->on('mg_employee')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_employee_tasks');
    }
};
