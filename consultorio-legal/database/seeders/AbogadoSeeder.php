<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AbogadoSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'martin.fernandez@ejemplo.test'],
            [
                'name' => 'Martín Alejandro Fernández',
                'password' => 'Abogado1234!',
                'rol' => 'abogado',
            ]
        );
    }
}
