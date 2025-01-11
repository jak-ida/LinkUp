<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('title'); // Meeting title
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table for host
            $table->foreignId('co_host_id')->nullable()->constrained('users')->onDelete('set null'); // Optional co-host
            $table->enum('type', ['online', 'offline']); // Meeting type
            $table->date('date'); // Meeting date
            $table->time('start_time'); // Meeting start time
            $table->time('end_time')->nullable(); // Meeting end time (optional)
            $table->string('venue')->nullable(); // Venue for offline meetings
            $table->string('meeting_link')->nullable(); // Link for online meetings
            $table->enum('state', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled'); // Meeting state
            $table->timestamps(); // created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
}
