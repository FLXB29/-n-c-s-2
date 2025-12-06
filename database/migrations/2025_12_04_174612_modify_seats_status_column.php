<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Thay đổi cột status từ ENUM cũ sang ENUM mới có thêm 'held'
        // Các giá trị: available, held, reserved, sold, blocked
        DB::statement("ALTER TABLE seats MODIFY COLUMN status ENUM('available', 'held', 'reserved', 'sold', 'blocked') DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Chuyển tất cả 'held' về 'available' trước khi revert
        DB::statement("UPDATE seats SET status = 'available' WHERE status = 'held'");
        DB::statement("ALTER TABLE seats MODIFY COLUMN status ENUM('available', 'reserved', 'sold', 'blocked') DEFAULT 'available'");
    }
};
