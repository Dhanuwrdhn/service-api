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
        Schema::create('mg_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('jobs_id');
            $table->unsignedBigInteger('project_id');
            $table->string('document_name');
            $table->string('document_desc');
            $table->unsignedBigInteger('creator_id');
            $table->string('document_file');
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('mg_employee')->onDelete('cascade');
             $table->foreign('role_id')->references('id')->on('mg_roles')->onDelete('cascade');
            $table->foreign('jobs_id')->references('id')->on('mg_jobs')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('mg_teams')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('mg_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_documents');
    }
};
