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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Trip title
            $table->text('description')->nullable(); // Trip details
            $table->date('start_date'); // Start date
            $table->date('end_date'); // End date
            $table->integer('max_participants')->default(10); // Max people allowed
            $table->string('location'); // Destination
            $table->decimal('cost', 10, 2)->nullable(); // Cost of the trip
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
