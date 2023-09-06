<?php

namespace App\GraphQL\Mutations\Area;

use App\Models\Area;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CreateAreaMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createArea',
        'description' => 'Creates a new Area'
    ];

    public function type(): Type
    {
        return GraphQL::type('Area');
    }

    public function args(): array
    {
        return [
            'title' => [
                'name' => 'title',
                'type' =>  Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $category = new Area();
        $category->fill($args);
        $category->save();

        return $category;
    }
}