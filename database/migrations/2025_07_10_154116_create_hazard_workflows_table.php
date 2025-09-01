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
        Schema::create('hazard_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // contoh: moderator, erm
            $table->string('from_status');
            $table->string('to_status');
            $table->string('from_inisial');
            $table->string('to_inisial');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hazard_workflows');
    }
};
