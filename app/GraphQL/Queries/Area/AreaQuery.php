<?php

namespace App\GraphQL\Queries\Area;

use App\Models\Area;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AreaQuery extends Query
{
    protected $attributes = [
        'name' => 'area',
    ];

    public function type(): Type
    {
        return GraphQL::type('Area');
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
        return Area::findOrFail($args['id']);
    }
}