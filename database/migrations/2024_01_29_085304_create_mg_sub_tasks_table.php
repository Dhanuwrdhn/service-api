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
            $table->unsignedBigInteger('task_id');
            $table->string('subtask_name');
            $table->string('subtask_description')->nullable();
            $table->unsignedBigInteger('assign_by');
            $table->datetime('start_date')->default(now()->toDateTimeString());
            $table->datetime('end_date');
            $table->enum('subtask_status', ['onPending','onReview', 'workingOnIt', 'Completed', ])->default('onPending');
            $table->enum('subtask_submit_status', ['earlyFinish', 'finish', 'finish in delay','overdue' ])->nullable();
            $table->string('subtask_percentage')->nullable();
            $table->string('subtask_image')->nullable(); // Ubah kolom untuk menyimpan gambar menjadi tipe data binary
            $table->timestamps();

            $table->foreign('assign_by')->references('id')->on('mg_employee')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('mg_tasks')->onDelete('cascade');
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
