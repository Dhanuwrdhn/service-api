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
        Schema::create('mg_clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_name')->unique();
            $table->string('client_type')->nullable();
            $table->string('client_address')->nullable();
            $table->string('client_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mg_clients');
    }
};
