<?php

namespace App\GraphQL\Types;

use App\Models\Area;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AreaType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Area',
        'description' => 'Collection of areas',
        'model' => Area::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of area'
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Title of the area'
            ]
        ];
    }
}