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
        Schema::create('mg_leave', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('handover_by')->nullable();
            $table->enum('leave_type',['leave_year','leave_sick','leave_special']);
            $table->datetime('start_date')->default(now()->toDateTimeString());
            $table->datetime('end_date')->nullable();
            $table->string('leave_reason')->nullable();
            $table->tinyInteger('isApproval'); //
            $table->enum('leave_status',['onPending','onReview','Allowed'])->nullable();
            $table->integer('total_days_leave')->nullable();// Set default value to 0
            $table->integer('total_leave_year')->nullable(); // Set default value to 0
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('mg_employee')->onDelete('cascade');
            $table->foreign('handover_by')->references('id')->on('mg_employee')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_leave');
    }
};
