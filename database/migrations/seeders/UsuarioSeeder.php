<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Comunidade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // criar admin padrão
        $admin = Usuario::factory()->create([
            'nome' => 'Admin',
            'email' => 'admin@site.com',
            'senha' => bcrypt('6~5~5y9%Rfq'),
            'tipo_usuario' => 1,
        ]);

        Comunidade::factory()->create([
            'usuario_id' => $admin->id,
        ]);

        Usuario::factory(15)->create();
    }
}
