<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade'); // Link to the meeting
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Optional user as attendee
            $table->string('name')->nullable(); // For external attendees
            $table->string('email')->nullable(); // For external attendees
            $table->enum('rsvp_status', ['pending', 'accepted', 'declined'])->default('pending'); // RSVP status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
}
