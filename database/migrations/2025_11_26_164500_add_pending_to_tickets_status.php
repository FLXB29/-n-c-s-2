<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add 'pending' to tickets status enum
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('active','used','cancelled','refunded','pending') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert is hard with enums
    }
};
