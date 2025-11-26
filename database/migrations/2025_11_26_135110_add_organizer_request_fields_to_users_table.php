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
        Schema::table('users', function (Blueprint $table) {
            // Trạng thái yêu cầu lên Organizer: null (chưa yêu cầu), pending, approved, rejected
            $table->string('organizer_request_status')->nullable()->default(null);
            // Thời gian gửi yêu cầu
            $table->timestamp('organizer_request_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['organizer_request_status', 'organizer_request_at']);
        });
    }
};
