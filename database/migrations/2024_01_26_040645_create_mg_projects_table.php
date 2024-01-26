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
        Schema::create('mg_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->foreignId('role_id')->constrained('mg_roles')->onDelete('cascade');
            $table->foreignId('jobs_id')->constrained('mg_jobs')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('mg_teams')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('mg_clients')->onDelete('cascade');
            $table->datetime('start_date')->default(new DateTime());
            $table->datetime('end_date')->default(new DateTime());
            $table->enum('project_status', ['Ongoing','workingOnIt', 'Completed', ])->nullable();
            $table->string('percentage')->nullable();
            $table->string('total_task_completed')->nullable();
            $table->string('total_task_created')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_projects');
    }
};
