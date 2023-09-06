<?php

namespace App\GraphQL\Types;

use App\Models\Area;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'Endpoint de usuários',
        'model' => User::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do usuário dentro do banco de dados'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'O nome do usuário dentro do banco de dados'
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'O e-mail do usuário dentro do banco de dados'
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'O tipo do usuário dentro do banco de dados'
            ],
        ];
    }
}