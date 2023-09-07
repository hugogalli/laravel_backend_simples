<?php

namespace App\GraphQL\Mutations\Cliente;

use App\Models\Cliente;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;

class DeleteClienteMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteCliente',
        'description' => 'deletes a Cliente'
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
            $cliente = Cliente::findOrFail($args['id']);
            return $cliente->delete() ? 'Cliente deletado' : 'Falha ao deletar o Cliente';
        } catch (\Exception $e) {
            return 'Cliente não encontrado'; // Mensagem personalizada se a Cliente não existir
        }
    }
}
