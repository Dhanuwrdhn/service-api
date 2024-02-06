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
        Schema::create('mg_sub_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('subtask_name');
            $table->string('subtask_description')->nullable();
            $table->foreignId('task_id')->constrained('mg_tasks')->onDelete('cascade');
            $table->datetime('start_date')->default(now()->toDateTimeString());
            $table->datetime('end_date');
            $table->enum('subtask_status', ['onPending','onReview', 'workingOnIt', 'Completed', ])->nullable();
            $table->enum('subtask_submit_status', ['earlyFinish', 'finish', 'finish in delay','overdue' ])->nullable();
            $table->string('subtask_percentage')->nullable();
            $table->string('subtask_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_sub_tasks');
    }
};
