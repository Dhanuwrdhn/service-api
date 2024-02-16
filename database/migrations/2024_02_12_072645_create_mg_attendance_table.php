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
        Schema::create('mg_attendance', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // Add the employee_id column
            $table->unsignedBigInteger('employee_id');

            // Now you can set it as a foreign key
            $table->foreign('employee_id')->references('id')->on('mg_employee')->onDelete('cascade');

            $table->timestamp('checkin')->nullable();
            $table->timestamp('checkout')->nullable();
            $table->string('status')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_attendance');
    }
};
