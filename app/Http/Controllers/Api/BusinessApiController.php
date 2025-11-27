<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BusinessApiController extends Controller
{
    /**
     * Get all active businesses with their locations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Business::where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude');

            // Filtrar por proximidad si se proporciona ubicación del usuario
            if ($request->has(['user_lat', 'user_lng', 'radius'])) {
                $userLat = $request->user_lat;
                $userLng = $request->user_lng;
                $radius = $request->radius; // en kilómetros

                // Fórmula Haversine para calcular distancia
                $query->selectRaw("
                    *,
                    ( 6371 * acos( cos( radians(?) ) *
                      cos( radians( latitude ) ) *
                      cos( radians( longitude ) - radians(?) ) +
                      sin( radians(?) ) *
                      sin( radians( latitude ) ) ) ) AS distance
                ", [$userLat, $userLng, $userLat])
                ->havingRaw('distance < ?', [$radius])
                ->orderBy('distance');
            }

            $businesses = $query->select([
                'business_id',
                'business_name',
                'photo',
                'address',
                'latitude',
                'longitude',
                'location_description',
                'phone',
                'theme'
            ])->get();

            // Formatear la respuesta
            $businesses->transform(function ($business) {
                return [
                    'id' => $business->business_id,
                    'name' => $business->business_name,
                    'photo' => $business->photo ? url($business->photo) : null,
                    'address' => $business->address,
                    'latitude' => (float) $business->latitude,
                    'longitude' => (float) $business->longitude,
                    'location_description' => $business->location_description,
                    'phone' => $business->phone,
                    'theme' => $business->theme,
                    'distance' => isset($business->distance) ? round($business->distance, 2) : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $businesses,
                'total' => $businesses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener negocios',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get business details by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $business = Business::where('business_id', $id)
                ->where('is_active', true)
                ->first();

            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Negocio no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $business->business_id,
                    'name' => $business->business_name,
                    'photo' => $business->photo ? url($business->photo) : null,
                    'address' => $business->address,
                    'latitude' => (float) $business->latitude,
                    'longitude' => (float) $business->longitude,
                    'location_description' => $business->location_description,
                    'phone' => $business->phone,
                    'theme' => $business->theme,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el negocio',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
