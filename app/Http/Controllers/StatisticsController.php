<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\User;

/**
 * @OA\Tag(
 *     name="Estadísticas",
 *     description="Endpoints para estadísticas de usuarios registrados"
 * )
 */
class StatisticsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/statistics/daily",
     *     summary="Estadísticas diarias de usuarios",
     *     tags={"Estadísticas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas diarias",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function daily(): JsonResponse
    {
        $statistics = User::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('fecha')
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'fecha' => $item->fecha,
                    'total' => $item->total,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/statistics/weekly",
     *     summary="Estadísticas semanales de usuarios",
     *     tags={"Estadísticas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas semanales",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function weekly(): JsonResponse
    {
        $statistics = User::select(
            DB::raw('YEAR(created_at) as año'),
            DB::raw('WEEK(created_at) as semana'),
            DB::raw('MIN(DATE(created_at)) as fecha_inicio'),
            DB::raw('MAX(DATE(created_at)) as fecha_fin'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('año', 'semana')
            ->orderBy('año', 'desc')
            ->orderBy('semana', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'año' => $item->año,
                    'semana' => $item->semana,
                    'fecha_inicio' => $item->fecha_inicio,
                    'fecha_fin' => $item->fecha_fin,
                    'total' => $item->total,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/statistics/monthly",
     *     summary="Estadísticas mensuales de usuarios",
     *     tags={"Estadísticas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas mensuales",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function monthly(): JsonResponse
    {
        $statistics = User::select(
            DB::raw('YEAR(created_at) as año'),
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as periodo'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('año', 'mes', 'periodo')
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'año' => $item->año,
                    'mes' => $item->mes,
                    'periodo' => $item->periodo,
                    'total' => $item->total,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }
}
