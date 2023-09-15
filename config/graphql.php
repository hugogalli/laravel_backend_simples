<?php

declare(strict_types = 1);

return [
    'route' => [
        // The prefix for routes; do NOT use a leading slash!
        'prefix' => 'graphql',

        // The controller/method to use in GraphQL request.
        // Also supported array syntax: `[\Rebing\GraphQL\GraphQLController::class, 'query']`
        'controller' => \Rebing\GraphQL\GraphQLController::class . '@query',

        // Any middleware for the graphql route group
        // This middleware will apply to all schemas
        'middleware' => [],

        // Additional route group attributes
        //
        // Example:
        //
        //'group_attributes' => ['guard' => 'auth:api']
        //
        'group_attributes' => [],
    ],

    // The name of the default schema
    // Used when the route group is directly accessed
    'default_schema' => 'default',

    'batching' => [
        // Whether to support GraphQL batching or not.
        // See e.g. https://www.apollographql.com/blog/batching-client-graphql-queries-a685f5bcd41b/
        // for pro and con
        'enable' => true,
    ],

    // The schemas for query and/or mutation. It expects an array of schemas to provide
    // both the 'query' fields and the 'mutation' fields.
    //
    // You can also provide a middleware that will only apply to the given schema
    //
    // Example:
    //
    //  'schemas' => [
    //      'default' => [
    //          'controller' => MyController::class . '@method',
    //          'query' => [
    //              App\GraphQL\Queries\UsersQuery::class,
    //          ],
    //          'mutation' => [
    //
    //          ]
    //      ],
    //      'user' => [
    //          'query' => [
    //              App\GraphQL\Queries\ProfileQuery::class,
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //      'user/me' => [
    //          'query' => [
    //              App\GraphQL\Queries\MyProfileQuery::class,
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //  ]
    //
    'schemas' => [
        'default' => [
            'query' => [
                'users' => \App\GraphQL\Queries\User\UsersQuery::class,
                'user' => \App\GraphQL\Queries\User\UserQuery::class,

                'areas' => \App\GraphQL\Queries\Area\AreasQuery::class,
                'area' => \App\GraphQL\Queries\Area\AreaQuery::class,

                'clientes' => \App\GraphQL\Queries\Cliente\ClientesQuery::class,
                'cliente' => \App\GraphQL\Queries\Cliente\ClienteQuery::class,

                'atendimentos' => \App\GraphQL\Queries\Atendimento\AtendimentosQuery::class,
                'atendimento' => \App\GraphQL\Queries\Atendimento\AtendimentoQuery::class,

                //Relatorios
                'clienteRelatorio' => \App\GraphQL\Queries\Relatorio\ClienteRelatorioQuery::class,
                'areaRelatorio' => \App\GraphQL\Queries\Relatorio\AreaRelatorioQuery::class,
                'analistaRelatorio' => \App\GraphQL\Queries\Relatorio\AnalistaRelatorioQuery::class,
                'tipoAtendimentoRelatorio' => \App\GraphQL\Queries\Relatorio\TipoAtendimentoRelatorioQuery::class,
                'atendimentosPendentesRelatorio' => \App\GraphQL\Queries\Relatorio\AtendimentosPendentesRelatorioQuery::class,

            ],
            'mutation' => [
                // ExampleMutation::class,
                'createArea' => \App\GraphQL\Mutations\Area\CreateAreaMutation::class,
                'updateArea' => \App\GraphQL\Mutations\Area\UpdateAreaMutation::class,
                'deleteArea' => \App\GraphQL\Mutations\Area\DeleteAreaMutation::class,

                'createCliente' => \App\GraphQL\Mutations\Cliente\CreateClienteMutation::class,
                'updateCliente' => \App\GraphQL\Mutations\Cliente\UpdateClienteMutation::class,
                'deleteCliente' => \App\GraphQL\Mutations\Cliente\DeleteClienteMutation::class,

                'associateArea' => \App\GraphQL\Mutations\ConnectAreaAnalista\AssociateAreaMutation::class,
                'dissociateArea' => \App\GraphQL\Mutations\ConnectAreaAnalista\DissociateAreaMutation::class,

                'createAtendimento' => \App\GraphQL\Mutations\Atendimento\CreateAtendimentoMutation::class,
                'deleteAtendimento' => \App\GraphQL\Mutations\Atendimento\DeleteAtendimentoMutation::class,
                'tomarPosse' => \App\GraphQL\Mutations\Atendimento\TomarPosseMutation::class,
                'transferirPosse' => \App\GraphQL\Mutations\Atendimento\TransferirPosseMutation::class,
                'concluirAtendimento' => \App\GraphQL\Mutations\Atendimento\ConcluirAtendimentoMutation::class,
            ],

            // The types only available in this schema
            'types' => [
                // ExampleType::class,

            ],

            // Laravel HTTP middleware

            //'middleware' => ['auth:api'],
            'middleware' => null,

            // Which HTTP methods to support; must be given in UPPERCASE!
            'method' => ['GET', 'POST'],

            // An array of middlewares, overrides the global ones
            'execution_middleware' => null,
        ],

        //CRIAR MUTATION DE LOGIN E REGISTER USANDO http://localhost:8000/graphql/auth?query=mutation{login(email:"atendente@gmail.com",password:"teste123")}
        //COLOCAR O TOKEN NO CABECALHO COM
        /*

  {"Authorization": "Bearer TOKEN_AQUI"}

         */

        'auth' => [
            'query' => [
            ],
            'mutation' => [
                'login' => \App\GraphQL\Mutations\Auth\LoginMutation::class,
                'register' => \App\GraphQL\Mutations\Auth\RegisterMutation::class,
            ],
            'types' => [

            ],

            'middleware' => [],
            'method' => ['GET', 'POST'],
            'execution_middleware' => null,
        ],
    ],

    // The global types available to all schemas.
    // You can then access it from the facade like this: GraphQL::type('user')
    //
    // Example:
    //
    // 'types' => [
    //     App\GraphQL\Types\UserType::class
    // ]
    //
    'types' => [
        // ExampleType::class,
        // ExampleRelationType::class,
        // \Rebing\GraphQL\Support\UploadType::class,
        'User' => App\GraphQL\Types\UserType::class,
        'Area' => App\GraphQL\Types\AreaType::class,
        'Cliente' => App\GraphQL\Types\ClienteType::class,
        'Atendimento' => App\GraphQL\Types\AtendimentoType::class,

        //Relatorios
        'ClienteRelatorio' => App\GraphQL\Types\ClienteRelatorioType::class,
        'AreaRelatorio' => App\GraphQL\Types\AreaRelatorioType::class,
        'AnalistaRelatorio' => App\GraphQL\Types\AnalistaRelatorioType::class,
        'TipoAtendimentoRelatorio' => App\GraphQL\Types\TipoAtendimentoRelatorioType::class,

    ],

    // This callable will be passed the Error object for each errors GraphQL catch.
    // The method should return an array representing the error.
    // Typically:
    // [
    //     'message' => '',
    //     'locations' => []
    // ]
    'error_formatter' => [\Rebing\GraphQL\GraphQL::class, 'formatError'],

    /*
     * Custom Error Handling
     *
     * Expected handler signature is: function (array $errors, callable $formatter): array
     *
     * The default handler will pass exceptions to laravel Error Handling mechanism
     */
    'errors_handler' => [\Rebing\GraphQL\GraphQL::class, 'handleErrors'],

    /*
     * Options to limit the query complexity and depth. See the doc
     * @ https://webonyx.github.io/graphql-php/security
     * for details. Disabled by default.
     */
    'security' => [
        'query_max_complexity' => null,
        'query_max_depth' => null,
        'disable_introspection' => false,
    ],

    /*
     * You can define your own pagination type.
     * Reference \Rebing\GraphQL\Support\PaginationType::class
     */
    'pagination_type' => \Rebing\GraphQL\Support\PaginationType::class,

    /*
     * You can define your own simple pagination type.
     * Reference \Rebing\GraphQL\Support\SimplePaginationType::class
     */
    'simple_pagination_type' => \Rebing\GraphQL\Support\SimplePaginationType::class,

    /*
     * Overrides the default field resolver
     * See http://webonyx.github.io/graphql-php/data-fetching/#default-field-resolver
     *
     * Example:
     *
     * ```php
     * 'defaultFieldResolver' => function ($root, $args, $context, $info) {
     * },
     * ```
     * or
     * ```php
     * 'defaultFieldResolver' => [SomeKlass::class, 'someMethod'],
     * ```
     */
    'defaultFieldResolver' => null,

    /*
     * Any headers that will be added to the response returned by the default controller
     */
    'headers' => [],

    /*
     * Any JSON encoding options when returning a response from the default controller
     * See http://php.net/manual/function.json-encode.php for the full list of options
     */
    'json_encoding_options' => 0,

    /*
     * Automatic Persisted Queries (APQ)
     * See https://www.apollographql.com/docs/apollo-server/performance/apq/
     *
     * Note 1: this requires the `AutomaticPersistedQueriesMiddleware` being enabled
     *
     * Note 2: even if APQ is disabled per configuration and, according to the "APQ specs" (see above),
     *         to return a correct response in case it's not enabled, the middleware needs to be active.
     *         Of course if you know you do not have a need for APQ, feel free to remove the middleware completely.
     */
    'apq' => [
        // Enable/Disable APQ - See https://www.apollographql.com/docs/apollo-server/performance/apq/#disabling-apq
        'enable' => env('GRAPHQL_APQ_ENABLE', false),

        // The cache driver used for APQ
        'cache_driver' => env('GRAPHQL_APQ_CACHE_DRIVER', config('cache.default')),

        // The cache prefix
        'cache_prefix' => config('cache.prefix') . ':graphql.apq',

        // The cache ttl in seconds - See https://www.apollographql.com/docs/apollo-server/performance/apq/#adjusting-cache-time-to-live-ttl
        'cache_ttl' => 300,
    ],

    /*
     * Execution middlewares
     */
    'execution_middleware' => [
        \Rebing\GraphQL\Support\ExecutionMiddleware\ValidateOperationParamsMiddleware::class,
        // AutomaticPersistedQueriesMiddleware listed even if APQ is disabled, see the docs for the `'apq'` configuration
        \Rebing\GraphQL\Support\ExecutionMiddleware\AutomaticPersistedQueriesMiddleware::class,
        \Rebing\GraphQL\Support\ExecutionMiddleware\AddAuthUserContextValueMiddleware::class,
        // \Rebing\GraphQL\Support\ExecutionMiddleware\UnusedVariablesMiddleware::class,
    ],
];
