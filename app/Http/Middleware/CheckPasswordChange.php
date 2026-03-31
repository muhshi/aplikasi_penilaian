<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->must_change_password) {
            if (!session()->has('password_warning_sent')) {
                \Filament\Notifications\Notification::make()
                    ->title('Keamanan Akun')
                    ->body('Anda menggunakan password default. Harap segera ganti password Anda.')
                    ->warning()
                    ->persistent()
                    ->send();
                
                session()->put('password_warning_sent', true);
            }
        }

        return $next($request);
    }
}
