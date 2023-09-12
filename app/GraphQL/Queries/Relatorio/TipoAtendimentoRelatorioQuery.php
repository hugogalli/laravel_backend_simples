<?php

namespace App\GraphQL\Queries\Relatorio;

use App\Models\Atendimento;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Carbon\Carbon;

class TipoAtendimentoRelatorioQuery extends Query
{
    protected $attributes = [
        'name' => 'tipoAtendimentoRelatorio',
        'description' => 'Relatório de tipos de atendimento',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('TipoAtendimentoRelatorio'));
    }

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'type' => Type::string(),
                'description' => 'Data para o relatório (formato: DD/MM/YYYY)',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        if (auth()->user()->type != 'gerente') {
            throw new \Exception('Acesso não autorizado para usuarios do tipo '. auth()->user()->type . '.');
        }
        
        $dataEscolhida = isset($args['data']) ? Carbon::createFromFormat('d/m/Y', $args['data'])->format('Y-m-d') : Carbon::today();

        $tiposAtendimento = Atendimento::distinct('tipo')
            ->whereNotNull('tipo')
            ->whereDate('created_at', $dataEscolhida)
            ->pluck('tipo');

        $result = [];
        foreach ($tiposAtendimento as $tipo) {
            $numAtendimentos = Atendimento::where('tipo', $tipo)
                ->whereDate('created_at', $dataEscolhida)
                ->count();

            $result[] = [
                'tipo' => $tipo,
                'num_atendimentos' => $numAtendimentos,
            ];
        }

        return $result;
    }
}

