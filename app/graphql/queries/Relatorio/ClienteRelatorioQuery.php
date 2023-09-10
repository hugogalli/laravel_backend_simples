<?php

namespace App\GraphQL\Queries\Relatorio;

use App\Models\Atendimento;
use App\Models\Cliente;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Carbon\Carbon;

class ClienteRelatorioQuery extends Query
{
    protected $attributes = [
        'name' => 'clienteRelatorio',
        'description' => 'Relatório de clientes',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('ClienteRelatorio'));
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

        $clientes = Atendimento::distinct('cliente_id')
            ->whereNotNull('cliente_id')
            ->whereDate('created_at', $dataEscolhida)
            ->pluck('cliente_id');

        $clientesContatados = Cliente::whereIn('id', $clientes)->get();

        $clientesComAtendimentos = $clientesContatados->map(function ($cliente) use ($dataEscolhida) {
            $numAtendimentos = Atendimento::where('cliente_id', $cliente->id)
                ->whereDate('created_at', $dataEscolhida)
                ->count();

            return [
                'id' => $cliente->id,
                'title' => $cliente->title,
                'num_atendimentos' => $numAtendimentos,
            ];
        });

        return $clientesComAtendimentos->toArray();
    }
}
