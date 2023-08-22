<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Tag(
 *     name="Áreas",
 *     description="Endpoints relacionados a manipulação de Áreas",
 * )
 */
class AreaController extends Controller
{

    /**
     * @OA\Schema(
     *     schema="Area",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="title", type="string", example="Area A"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     * )
     */

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/areas",
     *     summary="Lista de áreas",
     *     description="Retorna a lista de áreas",
     *     tags={"Áreas"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="areas", type="array", @OA\Items(ref="#/components/schemas/Area")),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     * )
     */
    public function index()
    {
        $areas = Area::all();
        return response()->json([
            'status' => 'success',
            'areas' => $areas,
        ]);
    }


    /**
     * @OA\Post(
     *     security={{"jwt_token":{}}},
     *     path="/api/area",
     *     summary="Criar uma nova área",
     *     description="Cria uma nova área",
     *     tags={"Áreas"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Nova Área", description="Título da área"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Área criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Área criada com sucesso"),
     *             @OA\Property(property="area", ref="#/components/schemas/Area"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $area = Area::create([
            'title' => $request->title,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'area created successfully',
            'area' => $area,
        ]);
    }


    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/area/{id}",
     *     summary="Detalhes de uma área específica",
     *     description="Retorna os detalhes de uma área específica",
     *     tags={"Áreas"},
     *     @OA\Parameter(
     *         name="id",
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
     *             @OA\Property(property="area", ref="#/components/schemas/Area"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Área não encontrada",
     *     ),
     * )
     */
    public function show($id)
    {
        $area = Area::find($id);
        return response()->json([
            'status' => 'success',
            'area' => $area,
        ]);
    }


    /**
     * @OA\Put(
     *     security={{"jwt_token":{}}},
     *     path="/api/area/{id}",
     *     summary="Atualizar uma área existente",
     *     description="Atualiza uma área existente",
     *     tags={"Áreas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da área",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Novo Título", description="Novo título da área"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Área atualizada com sucesso"),
     *             @OA\Property(property="area", ref="#/components/schemas/Area"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Área não encontrada",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $area = Area::find($id);
        $area->title = $request->title;
        $area->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Area updated successfully',
            'area' => $area,
        ]);
    }


    /**
     * @OA\Delete(
     *     security={{"jwt_token":{}}},
     *     path="/api/area/{id}",
     *     summary="Excluir uma área",
     *     description="Exclui uma área",
     *     tags={"Áreas"},
     *     @OA\Parameter(
     *         name="id",
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
     *             @OA\Property(property="message", type="string", example="Área excluída com sucesso"),
     *             @OA\Property(property="area", ref="#/components/schemas/Area"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Área não encontrada",
     *     ),
     * )
     */
    public function destroy($id)
    {
        $area = Area::find($id);
        $area->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Area deleted successfully',
            'area' => $area,
        ]);
    }
}
