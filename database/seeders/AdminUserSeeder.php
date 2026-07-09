<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'by0x_',
            'name' => 'Putra',
            'email' => 'by0x_@example.com',
            'password' => Hash::make('admin123'),
            'bio' => 'Web2 → Web3 Developer. Moove Ambassador. Building in public.',
        ]);
    }
}