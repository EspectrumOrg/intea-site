<?php

namespace Database\Seeders;

use App\Models\Genero;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Criar gêneros padrão no site
        Genero::factory()->create([
            'titulo' => 'masculino',
        ]);

        Genero::factory()->create([
            'titulo' => 'feminino',
        ]);

        Genero::factory()->create([
            'titulo' => 'não-binário',
        ]);

        Genero::factory()->create([
            'titulo' => 'prefere não informar',
        ]);

         $this->command->info('✅ Gêneros masculino, feminino, não-binário e "prefere não informar" criados!');
    }
}
