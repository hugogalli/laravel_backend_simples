<?php

namespace App\GraphQL\Queries\Relatorio;

use App\Models\Atendimento;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AtendimentosPendentesRelatorioQuery extends Query
{
    protected $attributes = [
        'name' => 'atendimentosPendentesRelatorio',
        'description' => 'Relatório de atendimentos pendentes por usuário',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Atendimento'));
    }

    public function resolve($root, $args)
    {   
        if (auth()->user()->type != 'gerente') {
            throw new \Exception('Acesso não autorizado para usuarios do tipo '. auth()->user()->type . '.');
        }
        
        $user = auth()->user(); // Obtém o usuário logado

        // Verifica se o usuário tem uma relação com áreas
        if ($user->areas->isEmpty()) {
            return []; // Se não tiver, retorna uma lista vazia
        }

        // Obtém as IDs das áreas em que o usuário está incluído
        $areasIds = $user->areas->pluck('id');

        // Consulta os atendimentos pendentes nas áreas em que o usuário está incluído
        $atendimentosPendentes = Atendimento::whereIn('area_id', $areasIds)
            ->where('status', 'pendente')
            ->orderBy('created_at', 'asc')
            ->get();

        return $atendimentosPendentes;
    }
}
