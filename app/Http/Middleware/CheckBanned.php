<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if ($user && $user->status_conta == 2) {
            $ultimoBanimento = $user->banimentos()->latest()->first();
            $motivo = $ultimoBanimento->motivo ?? 'Sua conta foi banida.';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('ban_reason', 'Sua conta foi banida. Para mais informações, por favor, acesse seu e-mail e contate a empresa.');
        }

        return $next($request);
    }
}
