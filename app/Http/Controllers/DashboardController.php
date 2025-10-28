<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Postagem;
use App\Models\Denuncia;
use App\Models\Tendencia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total de usuários cadastrados
        $totalUsuarios = Usuario::count();

        // Novos usuários hoje
        $usuariosHoje = Usuario::whereDate('created_at', Carbon::today())->count();

        // Novos usuários na semana
        $usuariosSemana = Usuario::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        // Novos usuários no mês
        $usuariosMes = Usuario::whereMonth('created_at', Carbon::now()->month)->count();

        // Gráfico dos últimos 7 dias
        $dias = collect();
        $usuariosPorDia = collect();
        $postagensPorDia = collect();
        $denunciasPorDia = collect();
        $tendenciasPorDia = collect(); 

        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i)->format('Y-m-d');
            $dias->push(Carbon::today()->subDays($i)->format('d/m'));

            // Usuários criados por dia
            $usuariosPorDia->push(
                Usuario::whereDate('created_at', $dia)->count()
            );

            // Postagens criadas por dia
            $postagensPorDia->push(
                Postagem::whereDate('created_at', $dia)->count()
            );

            // Denúncias criadas por dia (qualquer tipo: usuário, postagem, comentário)
            $denunciasPorDia->push(
                Denuncia::whereDate('created_at', $dia)->count()
            );

            // Tendências usadas por dia
            $tendenciasPorDia->push(
                Tendencia::whereDate('ultimo_uso', $dia)->count()
            );
        }

        // Dados para o gráfico de pizza (quantidade de usuários por tipo)
        $usuariosPorTipo = Usuario::select('tipo_usuario')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('tipo_usuario')
            ->pluck('total', 'tipo_usuario');

        // Mapeamento de nomes
        $mapaTipos = [
            1 => 'Administrador',
            2 => 'Autista',
            3 => 'Comunidade',
            4 => 'Profissional de Saúde', 
            5 => 'Responsável',
        ];

        // Substituir a chave numérica pelo nome
        $usuariosPorTipoNomes = collect($usuariosPorTipo)->mapWithKeys(function ($total, $tipo) use ($mapaTipos) {
            return [$mapaTipos[$tipo] ?? "Tipo {$tipo}" => $total];
        });

        // DADOS DAS TENDÊNCIAS
        $totalTendencias = Tendencia::count();
        $tendenciasHoje = Tendencia::whereDate('ultimo_uso', Carbon::today())->count();
        $tendenciasSemana = Tendencia::whereBetween('ultimo_uso', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        
        // Top 5 tendências mais populares
        $topTendencias = Tendencia::orderBy('contador_uso', 'desc')
            ->orderBy('ultimo_uso', 'desc')
            ->take(6)
            ->get();

        // Tendências criadas este mês
        $tendenciasMes = Tendencia::whereMonth('created_at', Carbon::now()->month)->count();

        return view('admin.dashboard.index', compact(
            'totalUsuarios',
            'usuariosHoje',
            'usuariosSemana',
            'usuariosMes',
            'dias',
            'usuariosPorDia',
            'postagensPorDia',
            'denunciasPorDia',
            'usuariosPorTipo',
            'usuariosPorTipoNomes',
            'totalTendencias',
            'tendenciasHoje',
            'tendenciasSemana',
            'tendenciasMes',
            'topTendencias',
            'tendenciasPorDia',
        ));
    }
}