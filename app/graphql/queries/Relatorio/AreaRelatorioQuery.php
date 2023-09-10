<?php

namespace App\GraphQL\Queries\Relatorio;

use App\Models\Atendimento;
use App\Models\Area;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AreaRelatorioQuery extends Query
{
    protected $attributes = [
        'name' => 'areaRelatorio',
        'description' => 'Relatório de áreas',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('AreaRelatorio'));
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

        // Se o argumento 'data' estiver definido e for válido, converta para o formato YYYY-MM-DD; caso contrário, use a data atual.
        $dataEscolhida = isset($args['data']) ? Carbon::createFromFormat('d/m/Y', $args['data'])->format('Y-m-d') : Carbon::today();

        $areasMaisBuscadas = Atendimento::select('area_id', DB::raw('COUNT(area_id) as num_atendimentos'))
            ->whereNotNull('area_id')
            ->whereDate('created_at', $dataEscolhida)
            ->groupBy('area_id')
            ->orderByDesc('num_atendimentos')
            ->get();

        $result = [];
        foreach ($areasMaisBuscadas as $area) {
            $areaInfo = Area::find($area->area_id);
            if ($areaInfo) {
                $result[] = [
                    'id' => $areaInfo->id,
                    'title' => $areaInfo->title,
                    'num_atendimentos' => $area->num_atendimentos,
                ];
            }
        }

        return $result;
    }
}