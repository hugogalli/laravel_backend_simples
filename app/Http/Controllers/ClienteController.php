<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Clientes",
 *     description="Endpoints relacionados a manipulação de Clientes",
 * )
 */
class ClienteController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Cliente",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="title", type="string", example="Cliente A"),
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
     *     path="/api/clientes",
     *     summary="Lista de clientes",
     *     description="Retorna a lista de clientes",
     *     tags={"Clientes"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="clientes", type="array", @OA\Items(ref="#/components/schemas/Cliente")),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     * )
     */
    public function getTodos()
    {
        $clientes = Cliente::all();
        return response()->json([
            'status' => 'success',
            'clientes' => $clientes,
        ]);
    }


    /**
     * @OA\Post(
     *     security={{"jwt_token":{}}},
     *     path="/api/cliente",
     *     summary="Criar um novo cliente",
     *     description="Cria um novo cliente",
     *     tags={"Clientes"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Novo Cliente", description="Título do cliente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Cliente criado com sucesso"),
     *             @OA\Property(property="cliente", ref="#/components/schemas/Cliente"),
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
    public function criarNovo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $cliente = Cliente::create([
            'title' => $request->title,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'cliente created successfully',
            'cliente' => $cliente,
        ]);
    }


    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/cliente/{id}",
     *     summary="Detalhes de um cliente específico",
     *     description="Retorna os detalhes de um cliente específico",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="cliente", ref="#/components/schemas/Cliente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado",
     *     ),
     * )
     */
    public function getClienteById($id)
    {
        $cliente = Cliente::find($id);
        return response()->json([
            'status' => 'success',
            'cliente' => $cliente,
        ]);
    }

    /**
     * @OA\Put(
     *     security={{"jwt_token":{}}},
     *     path="/api/cliente/{id}",
     *     summary="Atualizar um cliente existente",
     *     description="Atualiza um cliente existente",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Novo Título", description="Novo título do cliente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Cliente atualizado com sucesso"),
     *             @OA\Property(property="cliente", ref="#/components/schemas/Cliente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado",
     *     ),
     * )
     */
    public function atualizarTitulo(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $cliente = Cliente::find($id);
        $cliente->title = $request->title;
        $cliente->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cliente updated successfully',
            'cliente' => $cliente,
        ]);
    }


    /**
     * @OA\Delete(
     *     security={{"jwt_token":{}}},
     *     path="/api/cliente/{id}",
     *     summary="Excluir um cliente",
     *     description="Exclui um cliente",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Cliente excluído com sucesso"),
     *             @OA\Property(property="cliente", ref="#/components/schemas/Cliente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado",
     *     ),
     * )
     */
    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        $cliente->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cliente deleted successfully',
            'cliente' => $cliente,
        ]);
    }
}
