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
        return Type::string();
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
        try {
            $area = Area::findOrFail($args['id']);
            return $area->delete() ? 'Área deletada' : 'Falha ao deletar a área';
        } catch (\Exception $e) {
            return 'Área não encontrada'; // Mensagem personalizada se a área não existir
        }
    }
}
