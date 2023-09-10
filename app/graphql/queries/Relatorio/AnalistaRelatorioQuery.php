<?php

namespace App\GraphQL\Queries\Relatorio;

use App\Models\Atendimento;
use App\Models\Area;
use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalistaRelatorioQuery extends Query
{
    protected $attributes = [
        'name' => 'analistaRelatorio',
        'description' => 'Relatório de analistas',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('AnalistaRelatorio'));
    }

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'type' => Type::string(),
                'description' => 'Filtrar por data no formato DD/MM/YYYY',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        if (auth()->user()->type != 'gerente') {
            throw new \Exception('Acesso não autorizado para usuarios do tipo '. auth()->user()->type . '.');
        }
        
        $dataEscolhida = isset($args['data']) ? Carbon::createFromFormat('d/m/Y', $args['data']) : Carbon::today();

        $relatorioAnalistas = Atendimento::whereDate('created_at', $dataEscolhida)
            ->whereNotNull('analista_id')
            ->select('analista_id')
            ->groupBy('analista_id')
            ->with('analista')
            ->get()
            ->map(function ($atendimento) use ($dataEscolhida) {
                $totalAtendimentos = Atendimento::where('analista_id', $atendimento->analista_id)
                    ->whereDate('created_at', $dataEscolhida)
                    ->count();
                $totalAtendimentosConcluidos = Atendimento::where('analista_id', $atendimento->analista_id)
                    ->whereDate('created_at', $dataEscolhida)
                    ->where('status', 'concluido')
                    ->count();

                return [
                    'id' => $atendimento->analista_id,
                    'nome' => $atendimento->analista->name,
                    'total_atendimentos' => $totalAtendimentos,
                    'total_atendimentos_concluidos' => $totalAtendimentosConcluidos,
                ];
            });

        return $relatorioAnalistas->toArray();
    }
}