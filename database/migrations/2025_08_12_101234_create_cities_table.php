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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('city')->index();
            $table->string('netrange')->index();
            $table->string('cidr');
            $table->string('netname');
            $table->string('organization')->index();
            $table->char('country', 2)->default('BF');
            $table->string('admin_name')->index();
            $table->string('admin_email');
            $table->string('tech_name')->index();
            $table->string('tech_email');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
