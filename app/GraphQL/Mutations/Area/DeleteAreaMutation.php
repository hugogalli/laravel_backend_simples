<?php

// app/graphql/mutations/Area/DeleteAreaMutation

namespace App\GraphQL\Mutations\Area;

use App\Models\Area;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;

class DeleteAreaMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteArea',
        'description' => 'deletes a Area'
    ];

    public function type(): Type
    {
        //return Type::string();
        return Type::boolean();
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'rules' => ['required']
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $area = Area::find($args['id']);
        if ((bool)$area) {
            $area->delete();
            return True;
        }
        else {
            throw new \Exception('Area não encontrada');
        }
    }
}
