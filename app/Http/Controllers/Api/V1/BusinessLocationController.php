<?php

/**
 * ============================================
 * CETAM - Business Location Controller
 * ============================================
 *
 * @project     Order QR API
 * @file        BusinessLocationController.php
 * @description Controlador para endpoints de geolocalización de negocios
 * @author      CETAM Dev Team
 * @created     2025-11-26
 * @version     1.0.0
 * @copyright   CETAM © 2025
 *
 * ============================================
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BusinessLocationController extends Controller
{
    /**
     * Obtener todos los negocios con paginación
     *
     * @endpoint GET /api/v1/businesses
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Parámetros de paginación
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);

            // Validar que per_page no sea excesivo
            if ($perPage > 500) {
                $perPage = 500;
            }

            \Log::info('📋 Obteniendo todos los negocios', [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            // Query base
            $query = Business::active();

            // Filtros opcionales
            if ($request->filled('city')) {
                $query->where('city', $request->input('city'));
            }

            if ($request->filled('state')) {
                $query->where('state', $request->input('state'));
            }

            if ($request->filled('with_location') && $request->input('with_location') == '1') {
                $query->withPublicLocation();
            }

            // Ordenar y paginar
            $businesses = $query->orderBy('created_at', 'desc')
                               ->paginate($perPage);

            \Log::info('✅ Negocios obtenidos', [
                'total' => $businesses->total(),
                'current_page' => $businesses->currentPage(),
            ]);

            // Formatear resultados (items() para obtener solo los items de la paginación)
            $formattedBusinesses = $businesses->map(function ($business) {
                return [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'phone' => $business->phone,
                    'email' => $business->email,
                    'address' => $business->address,
                    'address_details' => $business->address_details ?? null,
                    'city' => $business->city,
                    'state' => $business->state,
                    'postal_code' => $business->postal_code,
                    'latitude' => $business->latitude ? (float) $business->latitude : null,
                    'longitude' => $business->longitude ? (float) $business->longitude : null,
                    'is_open' => true,
                    'rating' => null,
                    'total_reviews' => null,
                    'created_at' => $business->created_at,
                    'updated_at' => $business->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Negocios obtenidos exitosamente',
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => [
                        'current_page' => $businesses->currentPage(),
                        'per_page' => $businesses->perPage(),
                        'total' => $businesses->total(),
                        'total_pages' => $businesses->lastPage(),
                        'from' => $businesses->firstItem(),
                        'to' => $businesses->lastItem(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('❌ Error al obtener todos los negocios', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los negocios',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener negocios cercanos a la ubicación del usuario
     *
     * @endpoint GET /api/v1/businesses/nearby
     * @param Request $request
     * @return JsonResponse
     */
    public function nearby(Request $request): JsonResponse
    {
        // Validar parámetros de entrada
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'radius' => 'nullable|numeric|min:0.1|max:100',
            'limit' => 'nullable|integer|min:1|max:50',
            'page' => 'nullable|integer|min:1',
        ], [
            'latitude.required' => 'La latitud es requerida',
            'latitude.numeric' => 'La latitud debe ser un número',
            'latitude.min' => 'La latitud debe estar entre -90 y 90',
            'latitude.max' => 'La latitud debe estar entre -90 y 90',
            'longitude.required' => 'La longitud es requerida',
            'longitude.numeric' => 'La longitud debe ser un número',
            'longitude.min' => 'La longitud debe estar entre -180 y 180',
            'longitude.max' => 'La longitud debe estar entre -180 y 180',
            'radius.numeric' => 'El radio debe ser un número',
            'radius.min' => 'El radio mínimo es 0.1 km',
            'radius.max' => 'El radio máximo permitido es 100 km',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de ubicación inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $userLat = $request->input('latitude');
            $userLng = $request->input('longitude');
            $radius = $request->input('radius', 10); // default 10km
            $limit = $request->input('limit', 20);
            $page = $request->input('page', 1);

            // Obtener negocios cercanos usando el scope
            $query = Business::nearby($userLat, $userLng, $radius)
                ->active();

            // Aplicar paginación
            $businesses = $query->paginate($limit, ['*'], 'page', $page);

            // Verificar si hay resultados
            if ($businesses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron negocios cercanos',
                    'data' => [
                        'businesses' => [],
                        'search_radius_km' => $radius,
                        'user_location' => [
                            'latitude' => (float) $userLat,
                            'longitude' => (float) $userLng,
                        ]
                    ]
                ], 404);
            }

            // Formatear datos de respuesta
            $formattedBusinesses = $businesses->map(function ($business) {
                return [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'phone' => $business->phone,
                    'address' => $business->address,
                    'address_details' => $business->address_details,
                    'city' => $business->city,
                    'state' => $business->state,
                    'postal_code' => $business->postal_code,
                    'latitude' => (float) $business->latitude,
                    'longitude' => (float) $business->longitude,
                    'distance_km' => (float) $business->distance_km,
                    'is_open' => true, // TODO: Implementar lógica de horarios
                    'rating' => null, // TODO: Implementar sistema de ratings
                    'total_reviews' => null, // TODO: Implementar sistema de reviews
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => [
                        'current_page' => $businesses->currentPage(),
                        'per_page' => $businesses->perPage(),
                        'total' => $businesses->total(),
                        'last_page' => $businesses->lastPage(),
                        'from' => $businesses->firstItem(),
                        'to' => $businesses->lastItem(),
                    ],
                    'user_location' => [
                        'latitude' => (float) $userLat,
                        'longitude' => (float) $userLng,
                    ],
                    'search_radius_km' => $radius,
                ],
                'message' => 'Negocios cercanos obtenidos exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener negocios cercanos',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener detalle de un negocio con distancia opcional
     *
     * @endpoint GET /api/v1/businesses/{business_id}
     * @param Request $request
     * @param int $businessId
     * @return JsonResponse
     */
    public function show(Request $request, int $businessId): JsonResponse
    {
        // Validar parámetros opcionales de ubicación
        $validator = Validator::make($request->all(), [
            'user_latitude' => 'nullable|numeric|min:-90|max:90',
            'user_longitude' => 'nullable|numeric|min:-180|max:180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de ubicación inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $business = Business::where('business_id', $businessId)
                ->active()
                ->first();

            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Negocio no encontrado'
                ], 404);
            }

            // Calcular distancia si se proporciona ubicación del usuario
            $distanceKm = null;
            if ($request->has(['user_latitude', 'user_longitude'])) {
                $distanceKm = $business->distanceTo(
                    $request->input('user_latitude'),
                    $request->input('user_longitude')
                );
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'phone' => $business->phone,
                    'address' => $business->address,
                    'address_details' => $business->address_details,
                    'city' => $business->city,
                    'state' => $business->state,
                    'postal_code' => $business->postal_code,
                    'latitude' => (float) $business->latitude,
                    'longitude' => (float) $business->longitude,
                    'distance_km' => $distanceKm,
                    'is_open' => true, // TODO: Implementar lógica de horarios
                    'opening_hours' => null, // TODO: Implementar horarios de apertura
                    'rating' => null, // TODO: Implementar ratings
                    'total_reviews' => null, // TODO: Implementar reviews
                    'created_at' => $business->created_at,
                    'updated_at' => $business->updated_at,
                ],
                'message' => 'Detalle del negocio obtenido exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el negocio',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Buscar negocios por ciudad, estado o código postal
     * NOTA: Ahora funciona sin parámetros obligatorios para compatibilidad con app móvil
     *
     * @endpoint GET /api/v1/businesses/search
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        // Validar parámetros de búsqueda (todos opcionales)
        $validator = Validator::make($request->all(), [
            'query' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de búsqueda inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $query = Business::active();

            // Si NO hay filtros de búsqueda, retornar todos los negocios activos
            // Si hay filtros, aplicar filtro de ubicación pública
            $hasSearchFilters = $request->filled(['query', 'city', 'state', 'postal_code']);

            if ($hasSearchFilters) {
                // Solo aplicar filtro de ubicación pública si hay búsqueda específica
                $query->withPublicLocation();
            }

            // Búsqueda por nombre
            if ($request->filled('query')) {
                $searchTerm = $request->input('query');
                $query->where('business_name', 'LIKE', "%{$searchTerm}%");
            }

            // Filtrar por ciudad
            if ($request->filled('city')) {
                $query->where('city', $request->input('city'));
            }

            // Filtrar por estado
            if ($request->filled('state')) {
                $query->where('state', $request->input('state'));
            }

            // Filtrar por código postal
            if ($request->filled('postal_code')) {
                $query->where('postal_code', $request->input('postal_code'));
            }

            // Si se proporciona ubicación, calcular distancia y ordenar
            if ($request->has(['latitude', 'longitude'])) {
                $userLat = $request->input('latitude');
                $userLng = $request->input('longitude');

                $query->selectRaw(
                    '*, ( 6371 * acos( cos( radians(?) ) *
                    cos( radians( latitude ) ) *
                    cos( radians( longitude ) - radians(?) ) +
                    sin( radians(?) ) *
                    sin( radians( latitude ) ) ) ) AS distance_km',
                    [$userLat, $userLng, $userLat]
                )->orderBy('distance_km', 'asc');
            }

            // Paginación
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);
            $businesses = $query->paginate($perPage, ['*'], 'page', $page);

            // Formatear resultados
            $formattedBusinesses = $businesses->map(function ($business) {
                return [
                    'business_id' => $business->business_id,
                    'business_name' => $business->business_name,
                    'phone' => $business->phone,
                    'email' => $business->email,
                    'address' => $business->address,
                    'address_details' => $business->address_details ?? null,
                    'city' => $business->city,
                    'state' => $business->state,
                    'postal_code' => $business->postal_code,
                    'latitude' => $business->latitude ? (float) $business->latitude : null,
                    'longitude' => $business->longitude ? (float) $business->longitude : null,
                    'distance_km' => isset($business->distance_km) ? (float) $business->distance_km : null,
                    'is_open' => true,
                    'rating' => null,
                    'total_reviews' => null,
                    'created_at' => $business->created_at,
                    'updated_at' => $business->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Búsqueda completada exitosamente',
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => [
                        'current_page' => $businesses->currentPage(),
                        'per_page' => $businesses->perPage(),
                        'total' => $businesses->total(),
                        'total_pages' => $businesses->lastPage(),
                        'from' => $businesses->firstItem(),
                        'to' => $businesses->lastItem(),
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
