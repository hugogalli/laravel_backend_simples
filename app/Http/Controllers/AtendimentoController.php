<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Atendimentos",
 *     description="Endpoints relacionados a atendimentos",
 * )
 */
class AtendimentoController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Atendimento",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="title", type="string"),
     *     @OA\Property(property="tipo", type="string"),
     *     @OA\Property(property="description", type="string"),
     *     @OA\Property(property="user_id", type="integer"),
     *     @OA\Property(property="pessoa", type="string"),
     *     @OA\Property(property="status", type="string"),
     *     @OA\Property(property="cliente_id", type="integer"),
     *     @OA\Property(property="area_id", type="integer"),
     *     @OA\Property(property="analista_id", type="integer"),
     *     @OA\Property(property="info_adicional", type="string"),
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
     *     path="/api/atendimentos",
     *     summary="Lista de atendimentos",
     *     description="Retorna a lista de atendimentos",
     *     tags={"Atendimentos"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="atendimentos", type="array", @OA\Items(ref="#/components/schemas/Atendimento")),
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
        $atendimentos = Atendimento::join('users as criador', 'criador.id', 'atendimentos.user_id')
            ->join('clientes', 'clientes.id', 'atendimentos.cliente_id')
            ->leftJoin('users as analista', 'analista.id', 'atendimentos.analista_id')
            ->join('areas', 'areas.id', 'atendimentos.area_id')
            ->select('criador.name as criado_por', 'clientes.title as cliente', 'areas.title as area', 'analista.name as analista_responsavel', 'atendimentos.*')
            ->get();

        return response()->json([
            'status' => 'success',
            'atendimentos' => $atendimentos,
        ]);
    }


    /**
     * @OA\Post(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimento",
     *     summary="Criar um novo atendimento",
     *     description="Cria um novo atendimento",
     *     tags={"Atendimentos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Título do atendimento"),
     *             @OA\Property(property="tipo", type="string", example="problema"),
     *             @OA\Property(property="description", type="string", example="Descrição do atendimento"),
     *             @OA\Property(property="pessoa", type="string", example="Nome da pessoa"),
     *             @OA\Property(property="cliente_id", type="integer", example=1),
     *             @OA\Property(property="area_id", type="integer", example=1),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Atendimento criado com sucesso"),
     *             @OA\Property(property="atendimento", ref="#/components/schemas/Atendimento"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas atendentes podem criar novos atendimentos",
     *     ),
     * )
     */
    public function criarNovo(Request $request)
    {
        if (Auth::user()->type != 'atendente') {
            return response()->json([
                'status' => 'error',
                'message' => 'Apenas atendentes podem criar novos atendimentos.',
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'tipo' => ['required', 'string', 'max:255', 'in:problema,duvida,solicitacao'],
            'description' => 'required|string|max:255',
            'pessoa' => 'required|string|max:255',
            'cliente_id' => 'required',
            'area_id' => 'required',
        ]);

        $atendimento = Atendimento::create([
            'title' => $request->title,
            'tipo' => $request->tipo,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'pessoa' => $request->pessoa,
            'status' => 'pendente',
            'cliente_id' => $request->cliente_id,
            'area_id' => $request->area_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'atendimento created successfully',
            'atendimento' => $atendimento,
        ]);
    }

    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimento/{id}",
     *     summary="Detalhes de um atendimento especifico",
     *     description="Retorna os detalhes de um atendimento específico de acordo com o ID",
     *     tags={"Atendimentos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do atendimento",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="atendimento", ref="#/components/schemas/Atendimento"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Atendimento não encontrado",
     *     ),
     * )
     */
    public function getAtendimentoById($id)
    {
        $atendimento = Atendimento::leftJoin('users as analista', 'analista.id', '=', 'atendimentos.analista_id')
            ->join('users as criador', 'criador.id', '=', 'atendimentos.user_id')
            ->join('clientes', 'clientes.id', '=', 'atendimentos.cliente_id')
            ->join('areas', 'areas.id', '=', 'atendimentos.area_id')
            ->select(
                'criador.name as criado_por',
                'clientes.title as cliente',
                'areas.title as area',
                'analista.name as analista_responsavel',
                'atendimentos.*'
            )
            ->find($id);

        if (!$atendimento) {
            return response()->json([
                'status' => 'error',
                'message' => 'Atendimento not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'atendimento' => $atendimento,
        ]);
    }

    /**
     * @OA\Put(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimento/posse/{id}",
     *     summary="Tomar posse de um atendimento",
     *     description="Permite que um analista tome posse de um atendimento em andamento caso ele seja o responsavel pelo mesmo",
     *     tags={"Atendimentos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do atendimento",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Atendimento em andamento após tomar posse"),
     *             @OA\Property(property="atendimento", ref="#/components/schemas/Atendimento"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas analistas da area podem tomar posse de atendimentos",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Atendimento não encontrado",
     *     ),
     * )
     */
    public function tomarPosse($id)
    {
        // Verifique se o usuário autenticado é um analista
        if (Auth::user()->type != 'suporte') {
            return response()->json([
                'status' => 'error',
                'message' => 'Apenas analistas podem tomar posse de atendimentos.',
            ], 403);
        }

        $atendimento = Atendimento::find($id);

        $analistaId = Auth::id();
        $areaId = $atendimento->area_id;

        $user = User::findOrFail($analistaId);
        if (!$user->areas->contains($areaId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Apenas analistas da area podem tomar posse de atendimentos.',
            ], 403);
        }

        $atendimento->status = 'em andamento';
        $atendimento->analista_id = $analistaId;
        $atendimento->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Atendimento em andamento após tomar posse',
            'atendimento' => $atendimento,
        ]);
    }


    /**
     * @OA\Put(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimento/transferir/{atendimentoId}/analista/{analistaId}",
     *     summary="Transferir um atendimento",
     *     description="Permite que um atendente transfira um atendimento para outro analista",
     *     tags={"Atendimentos"},
     *     @OA\Parameter(
     *         name="atendimentoId",
     *         in="path",
     *         required=true,
     *         description="ID do atendimento",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         name="analistaId",
     *         in="path",
     *         required=true,
     *         description="ID do analista para transferência",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Atendimento transferido com sucesso"),
     *             @OA\Property(property="atendimento", ref="#/components/schemas/Atendimento"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas atendentes podem transferir atendimentos ou analista designado não trabalha na area do atendimento",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Atendimento ou analista não encontrado",
     *     ),
     * )
     */
    public function transferirPosse($atendimentoId, $analistaId)
    {
        // Verifique se o usuário autenticado é um atendente
        if (Auth::user()->type != 'atendente') {
            return response()->json([
                'status' => 'error',
                'message' => 'Apenas atendentes podem transferir atendimentos.',
            ], 403);
        }

        $user = User::findOrFail($analistaId);

        // Verifique se o usuário autenticado é um atendente
        if ($user->type != 'suporte') {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuarios do tipo ' . $user->type . ' não podem receber posse de atendimentos.',
            ], 403);
        }

        $nome = $user->name;
        $atendimento = Atendimento::find($atendimentoId);
        $areaId = $atendimento->area_id;


        if (!$user->areas->contains($areaId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Apenas analistas da area podem receber posse de atendimentos.',
            ], 403);
        }

        $atendimento->status = 'em andamento';
        $atendimento->analista_id = $analistaId;
        $atendimento->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Atendimento em posse do analista ' . $nome,
            'atendimento' => $atendimento,
        ]);
    }

    /**
     * @OA\Put(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimento/completar/{id}",
     *     summary="Completar um atendimento",
     *     description="Permite que o analista responsável marque um atendimento como concluído",
     *     tags={"Atendimentos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do atendimento",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="info_adicional", type="string", example="Informações adicionais sobre o atendimento"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Atendimento marcado como concluído"),
     *             @OA\Property(property="atendimento", ref="#/components/schemas/Atendimento"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas suportes podem completar atendimentos ou o usuário não é o dono do atendimento",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Atendimento não encontrado",
     *     ),
     * )
     */

    public function concluir(Request $request, $id)
    {
        $atendimento = Atendimento::find($id);
        $analistaId = Auth::id();

        if (Auth::user()->type != 'suporte') {
            return response()->json([
                'status' => 'error',
                'message' => 'Apenas suportes podem completar atendimentos.',
            ], 403);
        }

        if ($analistaId != $atendimento->analista_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Você não é o dono desse atendimento',
            ], 403);
        }

        $request->validate([
            'info_adicional' => 'required|string|max:255',
        ]);

        $atendimento = Atendimento::leftJoin('users as analista', 'analista.id', '=', 'atendimentos.analista_id')
            ->join('users as criador', 'criador.id', '=', 'atendimentos.user_id')
            ->join('clientes', 'clientes.id', '=', 'atendimentos.cliente_id')
            ->join('areas', 'areas.id', '=', 'atendimentos.area_id')
            ->select(
                'criador.name as criado_por',
                'clientes.title as cliente',
                'areas.title as area',
                'analista.name as analista_responsavel',
                'atendimentos.*'
            )
            ->find($id);

        $atendimento->status = 'concluido';
        $atendimento->info_adicional = $request->info_adicional;
        $atendimento->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Atendimento Finalizado',
            'atendimento' => $atendimento,
        ]);
    }


    /**
     * @OA\Delete(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimento/{id}",
     *     summary="Excluir um atendimento",
     *     description="Permite que um usuario exclua um atendimento",
     *     tags={"Atendimentos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do atendimento",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Atendimento excluído com sucesso"),
     *             @OA\Property(property="atendimento", ref="#/components/schemas/Atendimento"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Atendimento não encontrado",
     *     ),
     * )
     */
    public function destroy($id)
    {
        $atendimento = Atendimento::find($id);
        $atendimento->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'atendimento deleted successfully',
            'atendimento' => $atendimento,
        ]);
    }


    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimentos/relatorios/clienteshoje",
     *     summary="Relatório de clientes contatados",
     *     description="Retorna o relatório de clientes que entraram em contato hoje",
     *     tags={"Relatórios"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="clientes_contatados", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="Nome do Cliente 1"),
     *                 @OA\Property(property="num_atendimentos", type="integer", example=2),
     *             )),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas gerentes podem acessar este relatório",
     *     ),
     * )
     */

    public function clientes()
    {
        // Verifique se o usuário autenticado é um gerente
        if (Auth::user()->type != 'gerente') {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        $today = Carbon::today();

        $clientes = Atendimento::distinct('cliente_id')
            ->whereNotNull('cliente_id')
            ->whereDate('created_at', $today)
            ->pluck('cliente_id');

        $clientesContatados = Cliente::whereIn('id', $clientes)->get();

        $clientesComAtendimentos = $clientesContatados->map(function ($cliente) use ($today) {
            $numAtendimentos = Atendimento::where('cliente_id', $cliente->id)
                ->whereDate('created_at', $today)
                ->count();

            return [
                'id' => $cliente->id,
                'nome' => $cliente->title,
                'num_atendimentos' => $numAtendimentos,
            ];
        });

        return response()->json([
            'status' => 'success',
            'clientes_contatados' => $clientesComAtendimentos,
        ]);
    }


    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimentos/relatorios/analistashoje",
     *     summary="Relatório de atendimentos por analista",
     *     description="Retorna o relatório de atendimentos atendidos por usuarios de suporte no dia de hoje",
     *     tags={"Relatórios"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="atendimentos_por_analista", type="array", @OA\Items(
     *                 @OA\Property(property="analista_id", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="Nome do Analista"),
     *                 @OA\Property(property="num_total_atendimentos", type="integer", example=5),
     *                 @OA\Property(property="num_atendimentos_concluidos", type="integer", example=3),
     *             )),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas gerentes podem acessar este relatório",
     *     ),
     * )
     */
    public function analistas()
    {
        if (Auth::user()->type != 'gerente') {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        $today = Carbon::today();

        $atendimentos = Atendimento::whereDate('created_at', $today)
            ->whereNotNull('analista_id')
            ->get();

        $totaisPorAnalista = [];

        foreach ($atendimentos as $atendimento) {
            $analistaId = $atendimento->analista_id;

            if (!isset($totaisPorAnalista[$analistaId])) {
                $totaisPorAnalista[$analistaId] = [
                    'analista_id' => $analistaId,
                    'nome' => $atendimento->analista->name,
                    'num_total_atendimentos' => 0,
                    'num_atendimentos_concluidos' => 0,
                ];
            }

            $totaisPorAnalista[$analistaId]['num_total_atendimentos']++;
            if ($atendimento->status === 'concluido') {
                $totaisPorAnalista[$analistaId]['num_atendimentos_concluidos']++;
            }
        }

        $resultado = array_values($totaisPorAnalista);

        return response()->json([
            'status' => 'success',
            'atendimentos_por_analista' => $resultado,
        ]);
    }


    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimentos/relatorios/areashoje",
     *     summary="Relatório de atendimentos por área",
     *     description="Retorna o relatório de atendimentos por área no dia de hoje",
     *     tags={"Relatórios"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="areas_procuradas", type="array", @OA\Items(
     *                 @OA\Property(property="area", type="string", example="Área 1"),
     *                 @OA\Property(property="num_atendimentos", type="integer", example=8),
     *             )),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas gerentes podem acessar este relatório",
     *     ),
     * )
     */

    public function areas()
    {
        // Verifique se o usuário autenticado é um gerente
        if (Auth::user()->type != 'gerente') {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        $today = Carbon::today();

        $areas = Atendimento::distinct('area_id')
            ->whereNotNull('area_id')
            ->whereDate('created_at', $today)
            ->pluck('area_id');

        $areasProcuradas = Area::whereIn('id', $areas)->get();

        $result = [];
        foreach ($areasProcuradas as $area) {
            $numAtendimentos = Atendimento::where('area_id', $area->id)
                ->whereDate('created_at', $today)
                ->count();

            $result[] = [
                'area' => $area->title,
                'num_atendimentos' => $numAtendimentos,
            ];
        }

        return response()->json([
            'status' => 'success',
            'areas_procuradas' => $result,
        ]);
    }

    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimentos/relatorios/tiposhoje",
     *     summary="Relatório de atendimentos por tipo",
     *     description="Retorna o relatório de atendimentos por tipo no dia de hoje",
     *     tags={"Relatórios"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="tipos_atendimento", type="array", @OA\Items(
     *                 @OA\Property(property="tipo", type="string", example="problema"),
     *                 @OA\Property(property="num_atendimentos", type="integer", example=10),
     *             )),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas gerentes podem acessar este relatório",
     *     ),
     * )
     */

    public function tipos()
    {
        if (Auth::user()->type != 'gerente') {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        $today = Carbon::today();

        $tiposAtendimento = Atendimento::distinct('tipo')
            ->whereNotNull('tipo')
            ->whereDate('created_at', $today)
            ->pluck('tipo');

        $result = [];
        foreach ($tiposAtendimento as $tipo) {
            $numAtendimentos = Atendimento::where('tipo', $tipo)
                ->whereDate('created_at', $today)
                ->count();

            $result[] = [
                'tipo' => $tipo,
                'num_atendimentos' => $numAtendimentos,
            ];
        }

        return response()->json([
            'status' => 'success',
            'tipos_atendimento' => $result,
        ]);
    }


    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimentos/relatorios/pendenteshoje",
     *     summary="Relatório de atendimentos pendentes",
     *     description="Retorna o relatório de atendimentos pendentes hoje",
     *     tags={"Relatórios"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="atendimentos_pendentes_hoje", type="array", @OA\Items(
     *                 @OA\Property(property="title", type="string", example="Título do Atendimento"),
     *                 @OA\Property(property="description", type="string", example="Descrição do Atendimento"),
     *                 @OA\Property(property="pessoa", type="string", example="Nome da Pessoa"),
     *                 @OA\Property(property="cliente", type="string", example="Nome do Cliente"),
     *                 @OA\Property(property="area", type="string", example="Nome da Área"),
     *                 @OA\Property(property="tipo", type="string", example="problema"),
     *                 @OA\Property(property="atendente", type="string", example="Nome do Atendente"),
     *             )),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Apenas gerentes podem acessar este relatório",
     *     ),
     * )
     */
    public function pendentes()
    {
        if (Auth::user()->type != 'gerente') {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        $today = Carbon::today();

        $atendimentosPendentes = Atendimento::where('status', 'pendente')
            ->whereDate('created_at', $today)
            ->with(['cliente', 'area', 'user']) 
            ->get();

        $result = [];
        foreach ($atendimentosPendentes as $atendimento) {
            $result[] = [
                'title' => $atendimento->title,
                'description' => $atendimento->description,
                'pessoa' => $atendimento->pessoa,
                'cliente' => $atendimento->cliente->title,
                'area' => $atendimento->area->title,
                'tipo' => $atendimento->tipo,
                'atendente' => $atendimento->user->name,
            ];
        }

        return response()->json([
            'status' => 'success',
            'atendimentos_pendentes_hoje' => $result,
        ]);
    }


    /**
     * @OA\Get(
     *     security={{"jwt_token":{}}},
     *     path="/api/atendimentos/relatorios/pendentesporanalista",
     *     summary="Relatório de Atendimentos Pendentes para o Analista",
     *     description="Retorna um relatório de atendimentos pendentes que podem ser possuídos pelo analista logado, nas áreas em que ele está associado, mostrando primeiro os atendimentos mais antigos.",
     *     tags={"Relatórios"},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="atendimentos_pendentes", type="array", @OA\Items(ref="#/components/schemas/Atendimento")),
     *         ),
     *     ),
     * 
     *     @OA\Response(
     *         response=403,
     *         description="Acesso não autorizado. (Somente usuarios do tipo suporte podem acessar essa funcionalidade)",
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *     ),
     * )
     */
    public function pendentesPorAnalista()
    {
        if (Auth::user()->type != 'suporte') {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        $analistaId = Auth::id();

        $analista = User::findOrFail($analistaId);
        $areasDoAnalista = $analista->areas->pluck('id')->toArray();

        $atendimentosPendentes = Atendimento::where('status', 'pendente')
            ->whereIn('area_id', $areasDoAnalista)
            ->with(['cliente', 'area', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();

        $result = [];
        foreach ($atendimentosPendentes as $atendimento) {
            $result[] = [
                'title' => $atendimento->title,
                'description' => $atendimento->description,
                'pessoa' => $atendimento->pessoa,
                'cliente' => $atendimento->cliente->title,
                'area' => $atendimento->area->title,
                'tipo' => $atendimento->tipo,
                'atendente' => $atendimento->user->name,
            ];
        }

        return response()->json([
            'status' => 'success',
            'atendimentos_pendentes_para_analista' => $result,
        ]);
    }
}
