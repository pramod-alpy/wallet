<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('transactions')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        User::insert([
            [
                'name' => 'Demo User 1',
                'email' => 'demo1@gmail.com',
                'password' => Hash::make('password'),
                'balance' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Demo User 2',
                'email' => 'demo2@gmail.com',
                'password' => Hash::make('password'),
                'balance' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],           
        ]);
    }
}
