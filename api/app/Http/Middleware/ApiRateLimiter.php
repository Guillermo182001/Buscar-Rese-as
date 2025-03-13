<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtener la dirección IP o identificador del usuario
        $key = $request->ip();
        
        // Si el usuario está autenticado, usar su ID como parte de la clave
        if ($request->user()) {
            $key = 'user_'.$request->user()->id.'_'.$key;
        }
        
        // Determinar el límite basado en la ruta
        $routeName = $request->route()->getName() ?? $request->path();
        $limit = $this->getRouteLimit($routeName);
        
        // Aplicar el rate limiting
        $limiter = RateLimiter::attempt(
            'api:'.$routeName.':'.$key,
            $limit['attempts'],
            function() {
                return true;
            },
            $limit['decay']
        );
        
        // Si se excede el límite, devolver error 429
        if (! $limiter) {
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Has excedido el límite de solicitudes. Por favor, inténtalo de nuevo más tarde.',
                'retry_after' => RateLimiter::availableIn('api:'.$routeName.':'.$key)
            ], 429);
        }
        
        // Agregar encabezados de rate limiting a la respuesta
        $response = $next($request);
        
        if ($response instanceof Response) {
            $response->headers->add([
                'X-RateLimit-Limit' => $limit['attempts'],
                'X-RateLimit-Remaining' => RateLimiter::remaining('api:'.$routeName.':'.$key, $limit['attempts']),
                'X-RateLimit-Reset' => RateLimiter::availableIn('api:'.$routeName.':'.$key)
            ]);
        }
        
        return $response;
    }
    
    /**
     * Obtiene los límites de tasa para una ruta específica
     *
     * @param string $route
     * @return array
     */
    protected function getRouteLimit($route)
    {
        // Configurar límites específicos para diferentes rutas
        $limits = [
            // Rutas de reseñas con límites más estrictos para proteger las APIs externas
            'reviews/google' => [
                'attempts' => 10,    // 10 intentos
                'decay' => 60        // por minuto
            ],
            'reviews/facebook' => [
                'attempts' => 10,    // 10 intentos
                'decay' => 60        // por minuto
            ],
            // Rutas de prueba con límites más permisivos
            'test' => [
                'attempts' => 60,    // 60 intentos
                'decay' => 60        // por minuto
            ],
            'test-apis' => [
                'attempts' => 20,    // 20 intentos
                'decay' => 60        // por minuto
            ]
        ];
        
        // Buscar límites específicos o usar valores predeterminados
        foreach ($limits as $pattern => $limit) {
            if (str_contains($route, $pattern)) {
                return $limit;
            }
        }
        
        // Límite predeterminado para todas las demás rutas
        return [
            'attempts' => 30,    // 30 intentos
            'decay' => 60        // por minuto
        ];
    }
}

