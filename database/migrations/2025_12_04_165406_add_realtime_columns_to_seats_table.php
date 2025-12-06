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
        Schema::table('seats', function (Blueprint $table) {
            // Thêm cột để theo dõi ai đang giữ ghế (không dùng foreign key vì users.id có thể khác kiểu)
            if (!Schema::hasColumn('seats', 'held_by_user_id')) {
                $table->unsignedBigInteger('held_by_user_id')->nullable()->after('status');
                $table->index('held_by_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seats', function (Blueprint $table) {
            if (Schema::hasColumn('seats', 'held_by_user_id')) {
                $table->dropIndex(['held_by_user_id']);
                $table->dropColumn('held_by_user_id');
            }
        });
    }
};
