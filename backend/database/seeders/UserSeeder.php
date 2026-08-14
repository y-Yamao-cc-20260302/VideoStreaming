<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email' => 'a@a',
                'password' => '12345678',
                'name' => 'a',
                'nickname' => 'a',
                'avatar_path' => '',
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate($user);
        }
    }
}
