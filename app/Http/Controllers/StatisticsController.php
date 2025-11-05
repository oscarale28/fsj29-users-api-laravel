<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Exception;

/**
 * @OA\Tag(
 *     name="Statistics",
 *     description="Endpoints for registered user statistics"
 * )
 */
class StatisticsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/statistics/daily",
     *     summary="Daily user statistics",
     *     tags={"Statistics"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daily statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Token not provided")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error processing request")
     *         )
     *     )
     * )
     */
    public function daily(): JsonResponse
    {
        try {
            $statistics = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'registered_users' => $item->total,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving daily statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/statistics/weekly",
     *     summary="Weekly user statistics",
     *     tags={"Statistics"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Weekly statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Token not provided")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error processing request")
     *         )
     *     )
     * )
     */
    public function weekly(): JsonResponse
    {
        try {
            $statistics = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('WEEK(created_at) as week'),
                DB::raw('MIN(DATE(created_at)) as from_date'),
                DB::raw('MAX(DATE(created_at)) as to_date'),
                DB::raw('COUNT(*) as total')
            )
                ->groupBy('year', 'week')
                ->orderBy('year', 'desc')
                ->orderBy('week', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'year' => $item->year,
                        'week' => $item->week,
                        'from_date' => $item->from_date,
                        'to_date' => $item->to_date,
                        'registered_users' => $item->total,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving weekly statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/statistics/monthly",
     *     summary="Monthly user statistics",
     *     tags={"Statistics"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Monthly statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Token not provided")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error processing request")
     *         )
     *     )
     * )
     */
    public function monthly(): JsonResponse
    {
        try {
            $statistics = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
                DB::raw('COUNT(*) as total')
            )
                ->groupBy('year', 'month', 'period')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'year' => $item->year,
                        'month' => $item->month,
                        'period' => $item->period,
                        'registered_users' => $item->total,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving monthly statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
