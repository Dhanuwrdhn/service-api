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
            $table->unsignedBigInteger('assign_by');
            $table->string('task_name');
            $table->string('task_desc')->nullable();
            $table->datetime('start_date')->default(now()->toDateTimeString());
            $table->datetime('end_date');
            $table->enum('percentage_task',['0','5','10','15','20','25','30','35','40','45','50','55','60','65','70','75','80','85','90','95','100'])->nullable();
            $table->enum('task_status', ['onPending','onReview', 'workingOnIt', 'Completed', ])->default('onPending');
            $table->enum('task_submit_status', ['earlyFinish', 'finish', 'finish in delay','overdue' ])->nullable();
            $table->datetime('completed_date')->nullable();
            $table->string('task_image')->nullable();
            $table->string('task_reason')->nullable();
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
