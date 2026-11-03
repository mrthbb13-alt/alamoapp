<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario demo para desarrollo si es necesario
        \App\Models\User::updateOrCreate(
            ['email' => 'user@alamo.gob.mx'],
            [
                'name'     => 'Ciudadano Álamo',
                'username' => 'user',
                'phone'    => '765 123 4567',
                'colonia'  => 'Zona Centro',
                'password' => \Illuminate\Support\Facades\Hash::make('123'),
            ]
        );
    }
}
