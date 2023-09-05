<?php

namespace App\GraphQL\Queries\Area;

use App\Models\Area;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AreasQuery extends Query
{
    protected $attributes = [
        'name' => 'areas',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Area'));
    }

    public function resolve($root, $args)
    {
        return Area::all();
    }
}