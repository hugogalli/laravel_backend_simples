<?php

namespace App\GraphQL\Mutations\Area;

use App\Models\Area;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UpdateAreaMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateArea',
        'description' => 'Updates an Area'
    ];

    public function type(): Type
    {
        //return GraphQL::type('Area');
        return Type::string();
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' =>  Type::nonNull(Type::int()),
            ],
            'title' => [
                'name' => 'title',
                'type' =>  Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args)
    {   
        try {
            $area = Area::findOrFail($args['id']);
            $area->fill($args);
            $area->save();
            return 'Área atualizada';
        } catch (\Exception $e) {
            return 'Área não encontrada';
        }

    }
}
