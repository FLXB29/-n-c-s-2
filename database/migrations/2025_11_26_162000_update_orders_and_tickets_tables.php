<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_code')) {
                $table->string('order_code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id'); // Removed constrained() to avoid FK errors
            }
            if (!Schema::hasColumn('orders', 'event_id')) {
                $table->unsignedBigInteger('event_id'); // Removed constrained()
            }
            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('cod');
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('pending');
            }
            if (!Schema::hasColumn('orders', 'transaction_id')) {
                $table->string('transaction_id')->nullable();
            }
        });

        // Fill order_code for existing rows
        $orders = DB::table('orders')->whereNull('order_code')->orWhere('order_code', '')->get();
        foreach ($orders as $order) {
            DB::table('orders')->where('id', $order->id)->update([
                'order_code' => 'ORD-' . strtoupper(Str::random(10))
            ]);
        }

        // Add unique index for order_code
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->unique('order_code');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }

        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'ticket_code')) {
                $table->string('ticket_code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('tickets', 'event_id')) {
                $table->unsignedBigInteger('event_id'); // Removed constrained()
            }
            if (!Schema::hasColumn('tickets', 'ticket_type_id')) {
                $table->unsignedBigInteger('ticket_type_id'); // Removed constrained()
            }
            if (!Schema::hasColumn('tickets', 'user_id')) {
                $table->unsignedBigInteger('user_id'); // Removed constrained()
            }
            if (!Schema::hasColumn('tickets', 'order_id')) {
                $table->unsignedBigInteger('order_id'); // Removed constrained()
            }
            if (!Schema::hasColumn('tickets', 'price_paid')) {
                $table->decimal('price_paid', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('tickets', 'status')) {
                $table->string('status')->default('paid');
            }
            if (!Schema::hasColumn('tickets', 'check_in_time')) {
                $table->dateTime('check_in_time')->nullable();
            }
        });

        // Fill ticket_code for existing rows
        $tickets = DB::table('tickets')->whereNull('ticket_code')->orWhere('ticket_code', '')->get();
        foreach ($tickets as $ticket) {
            DB::table('tickets')->where('id', $ticket->id)->update([
                'ticket_code' => 'TKT-' . strtoupper(Str::random(12))
            ]);
        }

        // Add unique index for ticket_code
        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unique('ticket_code');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_code', 'payment_method', 'payment_status', 'transaction_id']);
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['ticket_code', 'price_paid', 'check_in_time']);
        });
    }
};
