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
        Schema::create('mg_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('mg_roles')->onDelete('cascade');
            $table->foreignId('jobs_id')->constrained('mg_jobs')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('mg_teams')->onDelete('cascade');
            $table->String('employee_name')->unique();
            $table->date('date_of_birth')->nullable();
            $table->String('age')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email'); // Remove 'unique' constraint
            $table->string('username');
            $table->string('password'); // No need for 'unique' constraint
            $table->enum ('gender', ['Male', 'Female'])->nullable();
            $table->string('religion')->nullable();
            $table->string('npwp_number')->nullable();
            $table->string('identity_number')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_employee');
    }
};
