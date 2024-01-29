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
        Schema::create('mg_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('mg_projects')->onDelete('cascade');
            $table->string('task_name');
            $table->string('task_description')->nullable();
            $table->datetime('start_date')->default(now()->toDateTimeString());
            $table->datetime('end_date');
            $table->string('assigned_by')->nullable();
            $table->string('assigned_to')->nullable();
            $table->string('percentage_task')->nullable();
            $table->string('total_subtask_completed')->nullable();
            $table->enum('task_status', ['onPending','onReview', 'workingOnIt', 'Completed', ])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_tasks');
    }
};
