<?php

namespace App\GraphQL\Mutations\ConnectAreaAnalista;

use App\Models\Area;
use App\Models\User;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DissociateAreaMutation extends Mutation
{
    protected $attributes = [
        'name' => 'dissociateArea',
        'description' => 'Remove o vinculo de um analista a uma area'
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'analistaId' => [
                'name' => 'analistaId',
                'type' =>  Type::nonNull(Type::int()),
            ],
            'areaId' => [
                'name' => 'areaId',
                'type' =>  Type::nonNull(Type::int()),
            ],
        ];
    }

    public function resolve($root, $args)
    {   
        $analista = User::findOrFail($args['analistaId']);

        if ($analista->type != "suporte") {
            return 'Somente usuarios do tipo suporte podem ser associados com áreas';
        }

        if (!$analista->areas->contains($args['areaId'])) {
            return 'Analista não esta associado com a área';
        }

        $area = Area::findOrFail($args['areaId']);
        $analista->areas()->detach($area);

        return "Analista {$analista->name} dessvinculado com sucesso da area {$area->title}";
    }
}
