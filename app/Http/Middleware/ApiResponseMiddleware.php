<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $originalData = $response->getData(true);

            $formattedResponse = [
                'code' => $response->getStatusCode(),
                'message' => $response->getStatusCode() === 200 ? 'OK' : 'Error',
                'data' => $originalData
            ];

            return response()->json($formattedResponse, $response->getStatusCode());
        }

        return $response;
    }
}
