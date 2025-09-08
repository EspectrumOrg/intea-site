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
            'nome' => 'Arthur L. Grey',
            'user' => 'Art Leywin',
            'apelido' => 'art',
            'email' => 'admin@site.com',
            'senha' => bcrypt('123456'),
            'data_nascimento' => '2001-01-01',
            'tipo_usuario' => 1,
            'foto' => 'arquivos/perfil/fotos/admin-pick.avif',
            'descricao' => 'Administrador do sistema, responsável pelo gerenciamento e supervisão de usuários.'
        ]);

        // Criar autista padrão
        $autista = Usuario::factory()->create([
            'nome' => 'Kauan Costa ',
            'user' => 'dormindo092',
            'email' => 'autista@site.com',
            'senha' => bcrypt('123456'),
            'tipo_usuario' => 2,
            'foto' => 'arquivos/perfil/fotos/autista-pick.avif',
            'descricao' => 'Usuário do espectro autista, compartilhando experiências pessoais e conquistas diárias.'
        ]);

        // criar comunidade padrão
        $comunidade = Usuario::factory()->create([
            'nome' => 'João G. Ribeiro',
            'user' => 'Joao G',
            'email' => 'comunidade@site.com',
            'senha' => bcrypt('123456'),
            'tipo_usuario' => 3,
            'foto' => 'arquivos/perfil/fotos/comunidade-pick.avif',
            'descricao' => 'Membro ativo da comunidade, interessado em apoio mútuo e inclusão social.'
        ]);

        // criar profissional saúde padrão
        $profissionalsaude = Usuario::factory()->create([
            'nome' => 'Ryan Ferreira Barbosa',
            'user' => 'RyanFerreiraOF',
            'email' => 'profissionalsaude@site.com',
            'senha' => bcrypt('123456'),
            'tipo_usuario' => 4,
            'foto' => 'arquivos/perfil/fotos/profissional-pick.avif',
            'descricao' => 'Psicólogo especializado em TEA, disponível para orientações e compartilhamento de conhecimento.'
        ]);

        // criar responsável padrão
        $responsavel = Usuario::factory()->create([
            'nome' => 'Danilo Sousa Cunha',
            'user' => 'Danilo Sousa Cunha',
            'email' => 'responsavel@site.com',
            'senha' => bcrypt('123456'),
            'tipo_usuario' => 5,
            'foto' => 'arquivos/perfil/fotos/responsavel-pick.avif',
            'descricao' => 'Responsável por um usuário do espectro autista, participando de atividades de suporte e acompanhamento.'
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
            'tipo_registro' => 'CRP',
            'registro_profissional' => '06-12345',
            'tipo_profissional' => 'Psicólogo',
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

        // 10 usuários do tipo comunidade
        $usuarios = [
            [
                'nome' => 'Carla Mendes',
                'user' => 'carla.mendes',
                'email' => 'carla.mendes@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-06.jpg',
                'descricao' => 'Mãe dedicada e participante ativa em grupos de apoio.'
            ],
            [
                'nome' => 'João Pereira',
                'user' => 'joao.pereira',
                'email' => 'joao.pereira@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-07.jpg',
                'descricao' => 'Estudante universitário interessado em inclusão social.'
            ],
            [
                'nome' => 'Dr. Lucas Farias',
                'user' => 'lucas.farias',
                'email' => 'lucas.farias@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-08.jpg',
                'descricao' => 'Psicólogo voluntário que contribui em debates na comunidade.'
            ],
            [
                'nome' => 'Mariana Silva',
                'user' => 'mariana.silva',
                'email' => 'mariana.silva@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-09.jpg',
                'descricao' => 'Apaixonada por leitura e sempre engajada em discussões sobre empatia.'
            ],
            [
                'nome' => 'Rafael Oliveira',
                'user' => 'rafael.oliveira',
                'email' => 'rafael.oliveira@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-10.jpg',
                'descricao' => 'Pai de um adolescente autista, busca compartilhar experiências.'
            ],
            [
                'nome' => 'Beatriz Costa',
                'user' => 'beatriz.costa',
                'email' => 'beatriz.costa@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-11.jpg',
                'descricao' => 'Estudante de pedagogia com foco em educação inclusiva.'
            ],
            [
                'nome' => 'André Gomes',
                'user' => 'andre.gomes',
                'email' => 'andre.gomes@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-12.jpg',
                'descricao' => 'Irmão de autista, participa ativamente em discussões sobre acessibilidade.'
            ],
            [
                'nome' => 'Fernanda Rocha',
                'user' => 'fernanda.rocha',
                'email' => 'fernanda.rocha@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-13.jpg',
                'descricao' => 'Universitária de psicologia, sempre trazendo reflexões científicas.'
            ],
            [
                'nome' => 'Diego Martins',
                'user' => 'diego.martins',
                'email' => 'diego.martins@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-14.jpg',
                'descricao' => 'Educador físico, fala sobre atividades e saúde no espectro.'
            ],
            [
                'nome' => 'Ana Paula Lima',
                'user' => 'ana.lima',
                'email' => 'ana.lima@site.com',
                'foto' => 'arquivos/perfil/fotos/perfil-15.jpg',
                'descricao' => 'Cuidadora e voluntária em projetos de inclusão escolar.'
            ],
        ];

        foreach ($usuarios as $u) {
            $usuario = Usuario::factory()->create([
                'nome' => $u['nome'],
                'user' => $u['user'],
                'email' => $u['email'],
                'senha' => bcrypt('123456'),
                'tipo_usuario' => 3,
                'foto' => $u['foto'],
                'descricao' => $u['descricao'] ?? null,
            ]);

            Comunidade::factory()->create([
                'usuario_id' => $usuario->id,
            ]);

            FoneUsuario::factory(2)->create([
                'usuario_id' => $usuario->id,
            ]);
        }
    }
}
