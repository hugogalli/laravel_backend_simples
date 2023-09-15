<?php

namespace App\GraphQL\Queries\Area;

use App\Models\Area;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AreaQuery extends Query
{

    public function authorize($root, array $args, $ctx, ResolveInfo $resolveInfo = null, Closure $getSelectFields = null): bool
    {
            //$this->auth = JWTAuth::parseToken()->authenticate();
            return (bool)!Auth::guest();
    }

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
