<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AtendimentoTest extends TestCase
{

    public function test_user_can_get_list_of_atendimentos()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Criar alguns atendimentos para testar

        $response = $this->getJson(route('atendimentos.index'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'atendimentos' => [
                    '*' => ['criado_por', 'cliente', 'area', 'analista_responsavel', 'title', 'description', 'pessoa', 'tipo', 'status', 'created_at', 'updated_at'],
                ],
            ]);
    }

    /** @test */
    public function atendente_can_create_atendimento()
    {
        $atendente = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($atendente);

        $cliente = Cliente::factory()->create();
        $area = Area::factory()->create();

        $atendimentoData = [
            'title' => 'New Atendimento',
            'tipo' => 'duvida',
            'description' => 'Test description',
            'pessoa' => 'John Doe',
            'cliente_id' => $cliente->id,
            'area_id' => $area->id,
        ];

        $response = $this->postJson(route('atendimento.store'), $atendimentoData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'atendimento created successfully',
                'atendimento' => [
                    'title' => 'New Atendimento',
                    'tipo' => 'duvida',
                    'description' => 'Test description',
                    'user_id' => $atendente->id,
                    'pessoa' => 'John Doe',
                    'status' => 'pendente',
                    'cliente_id' => $cliente->id,
                    'area_id' => $area->id,
                ],
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'atendimento' => [
                    'title',
                    'tipo',
                    'description',
                    'user_id',
                    'pessoa',
                    'status',
                    'cliente_id',
                    'area_id',
                    'updated_at',
                    'created_at',
                    'id',
                ],
            ]);

        $this->assertDatabaseHas('atendimentos', [
            'title' => 'New Atendimento',
            'tipo' => 'duvida',
            'description' => 'Test description',
            'user_id' => $atendente->id,
            'pessoa' => 'John Doe',
            'status' => 'pendente',
            'cliente_id' => $cliente->id,
            'area_id' => $area->id,
        ]);
    }

    public function test_atendente_cannot_create_atendimento_with_invalid_tipo()
    {
        $atendente = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($atendente);

        $cliente = Cliente::factory()->create();
        $area = Area::factory()->create();

        $atendimentoData = [
            'title' => 'New Atendimento',
            'tipo' => 'errado',
            'description' => 'Test description',
            'pessoa' => 'John Doe',
            'cliente_id' => $cliente->id,
            'area_id' => $area->id,
        ];

        $response = $this->postJson(route('atendimento.store'), $atendimentoData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_can_retrieve_atendimento_by_id()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $atendimento = Atendimento::factory()->create();

        $response = $this->getJson(route('atendimento.show', ['id' => $atendimento->id]), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'atendimento' => [
                    'title' => $atendimento->title,
                    'tipo' => $atendimento->tipo,
                ],
            ])
            ->assertJsonStructure([
                'status',
                'atendimento' => [
                    'title',
                    'tipo',
                ],
            ]);
    }

    /** @test */
    public function test_suporte_user_can_take_possession_of_existing_atendimento_in_same_area()
    {
        $suporteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($suporteUser);

        $area = Area::factory()->create();

        $response = $this->postJson(route('analista.associate', ['analistaId' => $suporteUser->id, 'areaId' => $area->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Analista associated with area successfully', // Ajuste a mensagem aqui
            ]);


        $atendimento = Atendimento::factory()->create(['area_id' => $area->id]);

        $response = $this->putJson(route('atendimento.posse', ['id' => $atendimento->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Atendimento em andamento após tomar posse',
                'atendimento' => [
                    'status' => 'em andamento',
                    'analista_id' => $suporteUser->id,
                ],
            ]);
    }

    public function test_user_without_association_cannot_take_possession()
    {

        $analistaUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($analistaUser);

        $area = Area::factory()->create();
        $atendimento = Atendimento::factory()->create(['area_id' => $area->id]);

        $response = $this->putJson(route('atendimento.posse', ['id' => $atendimento->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas analistas da area podem tomar posse de atendimentos.',
            ]);
    }


    public function test_gerente_user_cannot_take_possession()
    {
        $gerenteUser = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($gerenteUser);

        $area = Area::factory()->create();

        $response = $this->postJson(route('analista.associate', ['analistaId' => $gerenteUser->id, 'areaId' => $area->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Analista associated with area successfully',
            ]);


        $atendimento = Atendimento::factory()->create(['area_id' => $area->id]);

        $response = $this->putJson(route('atendimento.posse', ['id' => $atendimento->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas analistas podem tomar posse de atendimentos.',
            ]);
    }

    public function test_atendente_user_cannot_take_possession()
    {
        $gerenteUser = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($gerenteUser);

        $area = Area::factory()->create();

        $response = $this->postJson(route('analista.associate', ['analistaId' => $gerenteUser->id, 'areaId' => $area->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Analista associated with area successfully',
            ]);


        $atendimento = Atendimento::factory()->create(['area_id' => $area->id]);

        $response = $this->putJson(route('atendimento.posse', ['id' => $atendimento->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas analistas podem tomar posse de atendimentos.',
            ]);
    }

    public function test_atendente_can_transfer_atendimento_to_suporte_analista()
    {
        $atendenteUser = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($atendenteUser);

        $suporteUser = User::factory()->create(['type' => 'suporte']);

        $area = Area::factory()->create();

        $response = $this->postJson(route('analista.associate', ['analistaId' => $suporteUser->id, 'areaId' => $area->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Analista associated with area successfully',
            ]);

        $atendimento = Atendimento::factory()->create(['area_id' => $area->id]);

        $response = $this->putJson(route('atendimento.transferir', ['atendimentoId' => $atendimento->id, 'analistaId' => $suporteUser->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Atendimento em posse do analista ' . $suporteUser->name,
                'atendimento' => [
                    'status' => 'em andamento',
                    'analista_id' => $suporteUser->id,
                ],
            ]);
    }

    public function test_gerent_user_cannot_transfer_atendimento()
    {
        // Crie um usuário que não seja do tipo "atendente"
        $nonAtendenteUser = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($nonAtendenteUser);

        $suporteUser = User::factory()->create(['type' => 'suporte']);

        $area = Area::factory()->create();

        $response = $this->postJson(route('analista.associate', ['analistaId' => $suporteUser->id, 'areaId' => $area->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Analista associated with area successfully',
            ]);

        $atendimento = Atendimento::factory()->create(['area_id' => $area->id]);

        $response = $this->putJson(route('atendimento.transferir', ['atendimentoId' => $atendimento->id, 'analistaId' => $suporteUser->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas atendentes podem transferir atendimentos.',
            ]);
    }

    public function test_suporte_user_cannot_transfer_atendimento()
    {
        // Crie um usuário que não seja do tipo "atendente"
        $nonAtendenteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($nonAtendenteUser);

        $suporteUser = User::factory()->create(['type' => 'suporte']);

        $area = Area::factory()->create();

        $response = $this->postJson(route('analista.associate', ['analistaId' => $suporteUser->id, 'areaId' => $area->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Analista associated with area successfully',
            ]);

        $atendimento = Atendimento::factory()->create(['area_id' => $area->id]);

        $response = $this->putJson(route('atendimento.transferir', ['atendimentoId' => $atendimento->id, 'analistaId' => $suporteUser->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas atendentes podem transferir atendimentos.',
            ]);
    }

    public function test_atendente_user_cannot_transfer_atendimento_if_suporte_not_same_area()
    {
        // Crie um usuário que não seja do tipo "atendente"
        $nonAtendenteUser = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($nonAtendenteUser);

        $suporteUser = User::factory()->create(['type' => 'suporte']);
        $atendimento = Atendimento::factory()->create();

        $response = $this->putJson(route('atendimento.transferir', ['atendimentoId' => $atendimento->id, 'analistaId' => $suporteUser->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas analistas da area podem receber posse de atendimentos.',
            ]);
    }

    public function test_suporte_analista_responsavel_can_complete_atendimento()
    {
        // Crie um usuário suporte
        $suporteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($suporteUser);

        // Crie um atendimento associado à área com o usuário suporte como analista
        $atendimento = Atendimento::factory()->create([
            'analista_id' => $suporteUser->id,
        ]);

        // Defina as informações adicionais para o atendimento
        $infoAdicional = 'Informações adicionais para o atendimento concluído';

        // Faça uma solicitação para concluir o atendimento
        $response = $this->putJson(route('atendimento.completar', ['id' => $atendimento->id]), ['info_adicional' => $infoAdicional], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Atendimento Finalizado',
                'atendimento' => [
                    'status' => 'concluido',
                    'info_adicional' => $infoAdicional,
                ],
            ]);
    }

    public function test_suporte_analista_nao_responsavel_cannot_complete_atendimento()
    {
        // Crie um usuário suporte
        $suporteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($suporteUser);

        // Crie um atendimento associado à área com o usuário suporte como analista
        $atendimento = Atendimento::factory()->create();

        // Defina as informações adicionais para o atendimento
        $infoAdicional = 'Informações adicionais para o atendimento concluído';

        // Faça uma solicitação para concluir o atendimento
        $response = $this->putJson(route('atendimento.completar', ['id' => $atendimento->id]), ['info_adicional' => $infoAdicional], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Você não é o dono desse atendimento',
            ]);
    }

    public function test_atendente_cannot_complete_atendimento()
    {
        // Crie um usuário suporte
        $user = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($user);

        // Crie um atendimento associado à área com o usuário suporte como analista
        $atendimento = Atendimento::factory()->create();

        // Defina as informações adicionais para o atendimento
        $infoAdicional = 'Informações adicionais para o atendimento concluído';

        // Faça uma solicitação para concluir o atendimento
        $response = $this->putJson(route('atendimento.completar', ['id' => $atendimento->id]), ['info_adicional' => $infoAdicional], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas suportes podem completar atendimentos.',
            ]);
    }

    public function test_gerente_cannot_complete_atendimento()
    {
        // Crie um usuário suporte
        $user = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($user);

        // Crie um atendimento associado à área com o usuário suporte como analista
        $atendimento = Atendimento::factory()->create();

        // Defina as informações adicionais para o atendimento
        $infoAdicional = 'Informações adicionais para o atendimento concluído';

        // Faça uma solicitação para concluir o atendimento
        $response = $this->putJson(route('atendimento.completar', ['id' => $atendimento->id]), ['info_adicional' => $infoAdicional], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas suportes podem completar atendimentos.',
            ]);
    }

    public function test_user_can_delete_atendimento()
    {
        // Crie um usuário qualquer
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Crie um atendimento
        $atendimento = Atendimento::factory()->create();

        // Faça uma solicitação para deletar o atendimento
        $response = $this->deleteJson(route('atendimento.destroy', ['id' => $atendimento->id]), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'atendimento deleted successfully',
                'atendimento' => [
                    'id' => $atendimento->id,
                ],
            ]);

        // Verifique se o atendimento foi realmente deletado
        $this->assertNull(Atendimento::find($atendimento->id));
    }

    /** @test */
    public function test_user_cannot_retrieve_nonexistent_atendimento_by_id()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->getJson(route('atendimento.show', ['id' => 9999999999]), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Atendimento not found',
            ]);
    }

    public function test_gerente_can_view_clients_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.clienteshoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'clientes_contatados' => [
                    '*' => [
                        'id',
                        'nome',
                        'num_atendimentos',
                    ],
                ],
            ]);
    }

    public function test_gerente_can_view_analysts_report_today()
    {
        $gerenteUser = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.analistashoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'atendimentos_por_analista' => [
                    '*' => [
                        'analista_id',
                        'nome',
                        'num_total_atendimentos',
                        'num_atendimentos_concluidos',
                    ],
                ],
            ]);
    }

    public function test_gerente_can_view_areas_report_today()
    {
        $gerenteUser = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.areashoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'areas_procuradas' => [
                    '*' => [
                        'area',
                        'num_atendimentos',
                    ],
                ],
            ]);
    }

    public function test_gerente_can_view_types_report_today()
    {
        $gerenteUser = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.tiposhoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'tipos_atendimento' => [
                    '*' => [
                        'tipo',
                        'num_atendimentos',
                    ],
                ],
            ]);
    }

    public function test_gerente_can_view_pending_report_today()
    {
        $gerenteUser = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.pendenteshoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'atendimentos_pendentes_hoje' => [
                    '*' => [
                        'title',
                        'description',
                        'pessoa',
                        'cliente',
                        'area',
                        'tipo',
                        'atendente',
                    ],
                ],
            ]);
    }

    public function test_suporte_canot_view_clients_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.clienteshoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_suporte_canot_view_analysts_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.analistashoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_atendente_canot_view_analysts_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.analistashoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_suporte_canot_view_areas_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.areashoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_atendente_canot_view_areas_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.areashoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_suporte_canot_view_types_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.tiposhoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_atendente_canot_view_types_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.tiposhoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_suporte_canot_view_pending_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.pendenteshoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_atendente_canot_view_pending_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.pendenteshoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    public function test_atendente_canot_view_clients_report_today()
    {
        // Crie um usuário do tipo "gerente"
        $gerenteUser = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($gerenteUser);

        $response = $this->getJson(route('atendimentos.relatorio.clienteshoje'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acesso não autorizado.',
            ]);
    }

    /** @test */
    public function gerente_cannot_create_atendimento()
    {
        $gerente = User::factory()->create(['type' => 'gerente']);
        $token = auth()->login($gerente);

        $cliente = Cliente::factory()->create();
        $area = Area::factory()->create();

        $atendimentoData = [
            'title' => 'New Atendimento',
            'tipo' => 'problema',
            'description' => 'Test description',
            'pessoa' => 'John Doe',
            'cliente_id' => $cliente->id,
            'area_id' => $area->id,
        ];

        $response = $this->postJson(route('atendimento.store'), $atendimentoData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas atendentes podem criar novos atendimentos.',
            ]);
    }

    /** @test */
    public function suporte_cannot_create_atendimento()
    {
        $suporte = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($suporte);

        $cliente = Cliente::factory()->create();
        $area = Area::factory()->create();

        $atendimentoData = [
            'title' => 'New Atendimento',
            'tipo' => 'problema',
            'description' => 'Test description',
            'pessoa' => 'John Doe',
            'cliente_id' => $cliente->id,
            'area_id' => $area->id,
        ];

        $response = $this->postJson(route('atendimento.store'), $atendimentoData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Apenas atendentes podem criar novos atendimentos.',
            ]);
    }

    /** @test */
    public function analista_can_get_pendentes_por_analista_report()
    {
        // Crie um analista e uma área
        $analista = User::factory()->create(['type' => 'suporte']);
        $area = Area::factory()->create();
        $analista->areas()->attach($area);

        // Crie alguns atendimentos pendentes na área do analista
        $atendimentosPendentes = Atendimento::factory()->count(5)->create([
            'status' => 'pendente',
            'area_id' => $area->id,
        ]);

        // Autentique o analista
        $this->actingAs($analista, 'api');

        // Faça uma requisição para o relatório
        $response = $this->getJson(route('atendimentos.relatorio.pendentesporanalista'));

        // Verifique se a resposta está correta
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        // Verifique se a lista de atendimentos pendentes está correta
        $response->assertJsonStructure([
            'status',
            'atendimentos_pendentes_para_analista' => [
                '*' => [
                    'title',
                    'description',
                    'pessoa',
                    'cliente',
                    'area',
                    'tipo',
                    'atendente',
                ]
            ]
        ]);
    }

    /** @test */
    public function test_getente_cannot_get_pendentes_por_analista_report()
    {
        // Crie um usuário que não é um analista
        $user = User::factory()->create(['type' => 'gerente']);

        // Autentique o usuário
        $this->actingAs($user, 'api');

        // Faça uma requisição para o relatório
        $response = $this->getJson(route('atendimentos.relatorio.pendentesporanalista'));

        // Verifique se a resposta é de acesso não autorizado
        $response->assertStatus(403);
    }

    /** @test */
    public function test_atendente_cannot_get_pendentes_por_analista_report()
    {
        // Crie um usuário que não é um analista
        $user = User::factory()->create(['type' => 'atendente']);

        // Autentique o usuário
        $this->actingAs($user, 'api');

        // Faça uma requisição para o relatório
        $response = $this->getJson(route('atendimentos.relatorio.pendentesporanalista'));

        // Verifique se a resposta é de acesso não autorizado
        $response->assertStatus(403);
    }

    public function test_guests_cannot_access_atendimento_routes()
    {
        $atendimentoId = 1; // ID de um atendimento existente
        $analistaId = 1; // ID de um analista existente

        // 
        $getRoutesWithoutParameters = [
            'atendimentos.index',
            'atendimentos.relatorio.clienteshoje',
            'atendimentos.relatorio.analistashoje',
            'atendimentos.relatorio.areashoje',
            'atendimentos.relatorio.tiposhoje',
            'atendimentos.relatorio.pendenteshoje',
            'atendimentos.relatorio.pendentesporanalista',
        ];

        foreach ($getRoutesWithoutParameters as $routeName) {
            $response = $this->getJson(route($routeName));
            $response->assertStatus(401);
        }

        // Outros casos
        $response = $this->getJson(route('atendimento.show', [$atendimentoId]));
        $response->assertStatus(401);

        $response = $this->putJson(route('atendimento.posse', [$atendimentoId]));
        $response->assertStatus(401);

        $response = $this->putJson(route('atendimento.completar', [$atendimentoId]));
        $response->assertStatus(401);


        $response = $this->putJson(route('atendimento.transferir', [$atendimentoId, $analistaId]));
        $response->assertStatus(401);
    }
}
