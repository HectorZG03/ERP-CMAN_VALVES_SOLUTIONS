<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@cman.com',
            'password' => Hash::make('123456'),
            'role' => 'direccion'
        ]);

        User::create([
            'name' => 'TI',
            'email' => 'ti@cman.com',
            'password' => Hash::make('123456'),
            'role' => 'ti'
        ]);

        User::create([
            'name' => 'Almacén',
            'email' => 'almacen@cman.com',
            'password' => Hash::make('123456'),
            'role' => 'almacen'
        ]);

        User :: create ([
            'name' => 'Usuario',
            'email' => 'u@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'ti'
        ]);
    }
}