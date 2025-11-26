<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add 'cod' to payment_method enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('credit_card','bank_transfer','momo','zalopay','vnpay','cod') DEFAULT 'cod'");
        
        // Add 'paid' to status enum (if you want to use 'paid', otherwise use 'confirmed')
        // Let's stick to 'confirmed' in the controller to be safe, but expanding the enum is good practice if we want 'paid'
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','confirmed','cancelled','refunded','paid') DEFAULT 'pending'");

        // Add 'paid' to payment_status enum (currently: pending, completed, failed, refunded)
        // My controller uses 'paid', so I should add it or change controller to 'completed'
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','completed','failed','refunded','paid') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert is hard with enums without losing data, skipping for now
    }
};
