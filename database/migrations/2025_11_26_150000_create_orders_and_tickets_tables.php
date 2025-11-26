<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_code')->unique();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('event_id')->constrained()->onDelete('cascade');
                $table->decimal('total_amount', 12, 2);
                $table->string('status')->default('pending'); // pending, paid, cancelled
                $table->string('payment_method')->default('cod');
                $table->string('payment_status')->default('pending');
                $table->string('transaction_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_code')->unique();
                $table->foreignId('event_id')->constrained()->onDelete('cascade');
                $table->foreignId('ticket_type_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->decimal('price_paid', 12, 2);
                $table->string('status')->default('paid'); // paid, used, cancelled
                $table->dateTime('check_in_time')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('orders');
    }
};
