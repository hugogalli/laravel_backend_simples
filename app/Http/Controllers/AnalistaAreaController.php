<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Analista-Áreas",
 *     description="Endpoints relacionados a relação de usuários do tipo Suporte com suas áreas de atuação",
 * )
 */
class AnalistaAreaController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="AnalistaArea",
     *     title="AnalistaArea",
     *     description="Representa a associação entre um analista e uma área.",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="analista_id", type="integer", example=1, description="ID do analista"),
     *     @OA\Property(property="area_id", type="integer", example=1, description="ID da área"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     * )
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Post(
     *     security={{"jwt_token":{}}},
     *     path="/api/analistas/{analistaId}/areas/{areaId}",
     *     summary="Associar analista a área",
     *     description="Associa um analista a uma área específica. ",
     *     operationId="associateAnalistaArea",
     *     tags={"Analista-Áreas"},
     *     @OA\Parameter(
     *         name="analistaId",
     *         in="path",
     *         required=true,
     *         description="ID do analista",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         name="areaId",
     *         in="path",
     *         required=true,
     *         description="ID da área",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Analista associated with area successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Analista ou área não encontrados",
     *     ),
     * )
     */
    public function associate($analistaId, $areaId)
    {
        $analista = User::findOrFail($analistaId);
        $area = Area::findOrFail($areaId);

        $analista->areas()->attach($area);

        return response()->json([
            'status' => 'success',
            'message' => 'Analista associated with area successfully',
        ]);
    }

    /**
     * @OA\Post(
     *     security={{"jwt_token":{}}},
     *     path="/api/analista-areas/dissociate/{analistaId}/{areaId}",
     *     summary="Desassociar analista de área",
     *     description="Desassocia um analista de uma área específica.",
     *     operationId="dissociateAnalistaArea",
     *     tags={"Analista-Áreas"},
     *     @OA\Parameter(
     *         name="analistaId",
     *         in="path",
     *         required=true,
     *         description="ID do analista",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         name="areaId",
     *         in="path",
     *         required=true,
     *         description="ID da área",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Analista dissociated from area successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Analista ou área não encontrados",
     *     ),
     * )
     */
    public function dissociate($analistaId, $areaId)
    {
        $analista = User::findOrFail($analistaId);
        $area = Area::findOrFail($areaId);

        $analista->areas()->detach($area);

        return response()->json([
            'status' => 'success',
            'message' => 'Analista dissociated from area successfully',
        ]);
    }
}
