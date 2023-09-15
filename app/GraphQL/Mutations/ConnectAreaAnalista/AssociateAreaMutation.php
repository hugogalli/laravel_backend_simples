<?php

namespace App\GraphQL\Mutations\ConnectAreaAnalista;

use App\Models\Area;
use App\Models\User;
use Exception;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AssociateAreaMutation extends Mutation
{
    protected $attributes = [
        'name' => 'associateArea',
        'description' => 'Liga um analista a uma area'
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
            throw new Exception('Somente usuarios do tipo suporte podem ser associados com areas');
        }

        if ($analista->areas->contains($args['areaId'])) {
            throw new Exception('Analista já esta associado com a área');
        }

        $area = Area::findOrFail($args['areaId']);
        $analista->areas()->attach($area);

        return "Analista {$analista->name} associado com sucesso com area {$area->title}";
    }
}
