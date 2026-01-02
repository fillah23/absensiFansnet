<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Pengaturan;
use Symfony\Component\HttpFoundation\Response;

class CheckOfficeIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipKantor = Pengaturan::get('ip_kantor');
        $clientIp = $request->ip();

        if ($clientIp !== $ipKantor) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. IP tidak valid. Pastikan terhubung ke WiFi kantor.',
                'client_ip' => $clientIp,
                'required_ip' => $ipKantor
            ], 403);
        }

        return $next($request);
    }
}
