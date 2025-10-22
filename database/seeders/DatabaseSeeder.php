<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\jenisperangkat;
use Illuminate\Database\Seeder;
use App\Models\pengaturansistem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 
        user::create([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('qwerty0987654321'),
            'role' => 'Admin',
            'is_superadmin' => 1
        ]);

        pengaturansistem::create([
            'namapengaturan' => 'emailpenerima',
            'value' => 'muhammadarif29032004@gmail.com'
        ]);

        pengaturansistem::create([
            'namapengaturan' => 'emailsistem',
            'value' => 'josephangera730@gmail.com'
        ]);

        pengaturansistem::create([
            'namapengaturan' => 'sandiaplikasiemail',
            'value' => 'hpudaytvegkoaiml'
        ]);
    }
}
