<?php

namespace Tests\Feature\GraphQL\Auth;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthMutationTest extends TestCase
{
    public function testGuestCanRegister()
    {
        // Dados para registro
        $name = 'Teste PHPUnit';
        $email = 'qualqueremail@teste.com';
        $password = 'teste123';
        $type = 'atendente';

        // Consulta do GraphQL para criar um novo atendimento
        $query = '
        mutation(
            $name: String!
            $email: String!
            $password: String!
            $type: String!
        ){
            register(
                name: $name
                email: $email
                password: $password
                type: $type
                )
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'type' => $type,
        ];

        // Realize a consulta GraphQL com as variáveis
        $response = $this->postJson('/graphql/auth', [
            'query' => $query,
            'variables' => $variables,
        ]);

        // Verifique se o usuario foi criado corretamente.
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'register'
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
            'type' => $type
        ]);
    }

    public function testGuestCanLogin()
    {
        // Dados para registro
        $email = 'qualqueremail@teste.com';
        $password = 'teste123';

        // Consulta do GraphQL para criar um novo atendimento
        $query = '
        mutation(
            $email: String!
            $password: String!
        ){
            login(
                email: $email
                password: $password
                )
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'email' => $email,
            'password' => $password,
        ];

        // Realize a consulta GraphQL com as variáveis
        $response = $this->postJson('/graphql/auth', [
            'query' => $query,
            'variables' => $variables,
        ]);

        // Verifique se o usuario foi logado corretamente.
        $response->assertStatus(200)->assertJsonStructure([
                'data' => [
                    'login'
                ],
            ]);
    }
}
