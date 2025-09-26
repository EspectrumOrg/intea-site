<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
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

        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i)->format('Y-m-d');
            $dias->push(Carbon::today()->subDays($i)->format('d/m'));

            $usuariosPorDia->push(
                Usuario::whereDate('created_at', $dia)->count()
            );
        }

        return view('admin.dashboard.index', compact(
            'totalUsuarios',
            'usuariosHoje',
            'usuariosSemana',
            'usuariosMes',
            'dias',
            'usuariosPorDia'
        ));
    }
}
