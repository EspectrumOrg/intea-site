<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;


class DashboardController extends Controller
{
    public function index()
    {
        $totalUsuarios = usuario::count();

        return view('dashboard.index');
    }
}
