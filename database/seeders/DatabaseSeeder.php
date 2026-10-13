<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * 動作確認用のユーザー(パスワードはどちらも password)。
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => '管理者', 'password' => 'password', 'role' => UserRole::Admin, 'is_active' => true],
        );

        User::query()->updateOrCreate(
            ['email' => 'worker@example.com'],
            ['name' => '作業者', 'password' => 'password', 'role' => UserRole::Worker, 'is_active' => true],
        );
    }
}
