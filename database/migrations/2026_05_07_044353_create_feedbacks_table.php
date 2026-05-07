<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('salesperson_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('feedback_form_templates')->nullOnDelete();
            $table->json('data'); // key-value pairs from form submission
            $table->string('competitor_activity')->nullable();
            $table->decimal('order_value', 12, 2)->nullable();
            $table->unsignedTinyInteger('satisfaction_rating')->nullable();
            $table->json('photo_paths')->nullable();
            $table->text('voice_note_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
