<?php
// database/migrations/xxxx_create_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->string('featured_image', 500)->nullable();
            $table->json('gallery')->nullable();
            
            // Event details
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('timezone', 50)->default('Asia/Ho_Chi_Minh');
            
            // Location
            $table->string('venue_name')->nullable();
            $table->text('venue_address')->nullable();
            $table->string('venue_city', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Ticketing
            $table->boolean('is_free')->default(false);
            $table->integer('total_tickets')->default(0);
            $table->integer('tickets_sold')->default(0);
            $table->decimal('min_price', 12, 2)->nullable();
            $table->decimal('max_price', 12, 2)->nullable();
            
            // Settings
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            
            // Status
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            
            // Stats
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
            
            $table->index(['organizer_id', 'category_id', 'status', 'start_datetime', 'venue_city', 'is_featured']);
            $table->fullText(['title', 'description', 'venue_name', 'venue_city']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};