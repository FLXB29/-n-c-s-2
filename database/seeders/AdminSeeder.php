<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra xem admin đã tồn tại chưa để tránh trùng lặp
        if (!User::where('email', 'admin@eventhub.com')->exists()) {
            User::create([
                'full_name' => 'System Administrator',
                'email' => 'admin@eventhub.com',
                'phone' => '0999999999',
                'password_hash' => Hash::make('admin123'), // Mật khẩu mặc định
                'role' => 'admin',
                'status' => 'active',
            ]);
        }
    }
}
