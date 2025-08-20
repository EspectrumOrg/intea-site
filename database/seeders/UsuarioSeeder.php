<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\FoneUsuario;
use App\Models\Admin;
use App\Models\Autista;
use App\Models\Comunidade;
use App\Models\ProfissionalSaude;
use App\Models\Responsavel;
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
            'foto' => 'arquivos/perfil/fotos/admin-pick.avif',
        ]);

        // Criar autista padrão
        $autista = Usuario::factory()->create([
            'nome' => 'Matheus',
            'email' => 'autista@site.com',
            'senha' => bcrypt('<Pf&2>9N.£6'),
            'tipo_usuario' => 2,
            'foto' => 'arquivos/perfil/fotos/autista-pick.avif',
        ]);

        // criar comunidade padrão
        $comunidade = Usuario::factory()->create([
            'nome' => 'Comunidade',
            'email' => 'comunidade@site.com',
            'senha' => bcrypt('senhacomunidade'),
            'tipo_usuario' => 3,
            'foto' => 'arquivos/perfil/fotos/comunidade-pick.avif',
        ]);

        // criar profissional saúde padrão
        $profissionalsaude = Usuario::factory()->create([
            'nome' => 'Profissional Saúde',
            'email' => 'profissionalsaude@site.com',
            'senha' => bcrypt('$wjB?y17'),
            'tipo_usuario' => 4,
            'foto' => 'arquivos/perfil/fotos/profissional-pick.avif',
        ]);

        // criar responsável padrão
        $responsavel = Usuario::factory()->create([
            'nome' => 'Responsável',
            'email' => 'responsavel@site.com',
            'senha' => bcrypt('W4I3XpYy1'),
            'tipo_usuario' => 5,
            'foto' => 'arquivos/perfil/fotos/responsavel-pick.avif',
        ]);

        Admin::factory()->create([
            'usuario_id' => $admin->id,
        ]);

        FoneUsuario::factory(5)->create([
            'usuario_id' => $admin->id,
        ]);

        Autista::factory()->create([
            'usuario_id' => $autista->id,
        ]);

        FoneUsuario::factory(5)->create([
            'usuario_id' => $autista->id,
        ]);

        Comunidade::factory()->create([
            'usuario_id' => $comunidade->id,
        ]);

        FoneUsuario::factory(5)->create([
            'usuario_id' => $comunidade->id,
        ]);

        ProfissionalSaude::factory()->create([
            'usuario_id' => $profissionalsaude->id,
        ]);

        FoneUsuario::factory(5)->create([
            'usuario_id' => $profissionalsaude->id,
        ]);

        Responsavel::factory()->create([
            'usuario_id' => $responsavel->id,
        ]);

        FoneUsuario::factory(5)->create([
            'usuario_id' => $responsavel->id,
        ]);
    }
}
