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
            $table->unsignedBigInteger('project_id');
            $table->string('task_name');
            $table->string('task_description')->nullable();
            $table->unsignedBigInteger('assign_by');
            $table->datetime('start_date')->default(now()->toDateTimeString());
            $table->datetime('end_date');
            $table->string('percentage_task')->nullable();
            $table->string('total_subtask_completed')->nullable();
            $table->enum('task_status', ['onPending','onReview', 'workingOnIt', 'Completed', ])->nullable();
            $table->timestamps();

            $table->foreign('assign_by')->references('id')->on('mg_employee')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('mg_projects')->onDelete('cascade');
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
