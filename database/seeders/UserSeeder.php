<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ronak',
            'email' => 'ronak@pmproje.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Murat',
            'email' => 'murat@pmproje.com',
            'password' => Hash::make('password'),
        ]);
    }
}
