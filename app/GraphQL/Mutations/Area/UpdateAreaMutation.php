<?php

namespace App\GraphQL\Mutations\Area;

use App\Models\Area;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;

class UpdateAreaMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
        // Login necessario
        if (Auth::guest()) {
            throw new \Exception('Acesso não autorizado, favor realizar login no sistema');
        }

        // Demais verificações

        // Se não der exception continua normal
        return True;
    }

    protected $attributes = [
        'name' => 'updateArea',
        'description' => 'Updates an Area'
    ];

    public function type(): Type
    {
        //return GraphQL::type('Area');
        return Type::boolean();
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
        $area = Area::find($args['id']);

        if ((bool)$area) {
            $area->fill($args);
            $area->save();
            return True;
        } else {
            throw new \Exception('Area não encontrada');
        }
    }
}
