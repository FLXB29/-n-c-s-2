<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('event_comments')) {
            Schema::create('event_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('parent_id')->nullable(); // Cho reply
                $table->text('content');
                $table->tinyInteger('rating')->nullable(); // 1-5 sao
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();

                // Foreign keys
                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('parent_id')->references('id')->on('event_comments')->onDelete('cascade');

                // Indexes
                $table->index(['event_id', 'status']);
                $table->index('parent_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_comments');
    }
};
