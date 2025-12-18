<?php

/**
 * Company: CETAM
 * Project: FF
 * File: BusinessLocationController.php (API V1)
 * Created on: 26/11/2025
 * Created by: Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 16/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored to Business Location Controller standards |
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BusinessLocationController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->input('per_page', 20), 500);
            $page = $request->input('page', 1);

            Log::info('📋 Obteniendo todos los negocios', [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            // 5.5: Extract query building to private method
            $query = $this->buildIndexQuery($request);

            $businesses = $query->orderBy('created_at', 'desc')
                               ->paginate($perPage);

            Log::info('✅ Negocios obtenidos', [
                'total' => $businesses->total(),
                'current_page' => $businesses->currentPage(),
            ]);

            // 5.3: Use Collection map instead of foreach
            $formattedBusinesses = $businesses->map(
                fn($business) => $this->formatBusinessForIndex($business)
            );

            return response()->json([
                'success' => true,
                'message' => 'Negocios obtenidos exitosamente',
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => $this->formatPaginationData($businesses),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error(' Error al obtener todos los negocios', [
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

  
    public function nearby(Request $request): JsonResponse
    {
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

        // 5.4.1: Early Return - Validation failure
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
            $radius = $request->input('radius', 10);
            $limit = $request->input('limit', 20);
            $page = $request->input('page', 1);

            $query = Business::nearby($userLat, $userLng, $radius)
                ->active();

            $businesses = $query->paginate($limit, ['*'], 'page', $page);

            // 5.4.1: Early Return - No results found
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

            // 5.3: Use Collection map
            $formattedBusinesses = $businesses->map(
                fn($business) => $this->formatBusinessForNearby($business)
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => $this->formatPaginationData($businesses),
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

    public function show(Request $request, int $businessId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_latitude' => 'nullable|numeric|min:-90|max:90',
            'user_longitude' => 'nullable|numeric|min:-180|max:180',
        ]);

        // 5.4.1: Early Return - Validation failure
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

            // 5.4.1: Early Return - Not found
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Negocio no encontrado'
                ], 404);
            }

            // 5.5: Extract distance calculation to private method
            $distanceKm = $this->calculateDistance($business, $request);

            return response()->json([
                'success' => true,
                'data' => $this->formatBusinessForShow($business, $distanceKm),
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
     */
    public function search(Request $request): JsonResponse
    {
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

        // 5.4.1: Early Return - Validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetros de búsqueda inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // 5.5: Extract query building to private method
            $query = $this->buildSearchQuery($request);

            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);
            $businesses = $query->paginate($perPage, ['*'], 'page', $page);

            // 5.3: Use Collection map
            $formattedBusinesses = $businesses->map(
                fn($business) => $this->formatBusinessForSearch($business)
            );

            return response()->json([
                'success' => true,
                'message' => 'Búsqueda completada exitosamente',
                'data' => [
                    'businesses' => $formattedBusinesses,
                    'pagination' => $this->formatPaginationData($businesses),
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

    /**
     * Build query for index method
     * 5.5: Private helper method following SRP
     */
    private function buildIndexQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Business::active();

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        if ($request->filled('with_location') && $request->input('with_location') == '1') {
            $query->withPublicLocation();
        }

        return $query;
    }

    /**
     * Build query for search method
     * 5.5: Private helper method following SRP
     */
    private function buildSearchQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Business::active();

        $hasSearchFilters = $request->filled(['query', 'city', 'state', 'postal_code']);

        if ($hasSearchFilters) {
            $query->withPublicLocation();
        }

        if ($request->filled('query')) {
            $searchTerm = $request->input('query');
            $query->where('business_name', 'LIKE', "%{$searchTerm}%");
        }

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->filled('state')) {
            $query->where('state', $request->input('state'));
        }

        if ($request->filled('postal_code')) {
            $query->where('postal_code', $request->input('postal_code'));
        }

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

        return $query;
    }

    /**
     * Calculate distance if user location provided
     * 5.5: Private helper method following SRP
     */
    private function calculateDistance(Business $business, Request $request): ?float
    {
        // 5.4.1: Early Return - No user location
        if (!$request->has(['user_latitude', 'user_longitude'])) {
            return null;
        }

        return $business->distanceTo(
            $request->input('user_latitude'),
            $request->input('user_longitude')
        );
    }

    /**
     * Format business object for index response
     * 5.5: Private helper method following SRP - Eliminates code duplication
     */
    private function formatBusinessForIndex(Business $business): array
    {
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
    }

    /**
     * Format business object for nearby response
     * 5.5: Private helper method following SRP
     */
    private function formatBusinessForNearby(Business $business): array
    {
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
            'is_open' => true,
            'rating' => null,
            'total_reviews' => null,
        ];
    }

    /**
     * Format business object for show response
     * 5.5: Private helper method following SRP
     */
    private function formatBusinessForShow(Business $business, ?float $distanceKm): array
    {
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
            'distance_km' => $distanceKm,
            'is_open' => true,
            'opening_hours' => null,
            'rating' => null,
            'total_reviews' => null,
            'created_at' => $business->created_at,
            'updated_at' => $business->updated_at,
        ];
    }

    /**
     * Format business object for search response
     * 5.5: Private helper method following SRP
     */
    private function formatBusinessForSearch(Business $business): array
    {
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
    }

    /**
     * Format pagination data
     * 5.5: Private helper method following SRP - Eliminates code duplication
     */
    private function formatPaginationData($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'total_pages' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }
}
