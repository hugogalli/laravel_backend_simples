<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnalistaAreaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_associate_analista_with_area()
    {
        $analista = User::factory()->create(['type' => 'suporte']);
        $area = Area::factory()->create();
        $token = auth()->login($analista);

        $response = $this->postJson(
            route('analista.associate', ['analistaId' => $analista->id, 'areaId' => $area->id]),
            [],
            ['Authorization' => 'Bearer ' . $token]
        );

        $this->assertTrue($analista->areas->contains($area));
    }

    /** @test */
    public function test_user_can_dissociate_analista_from_area()
    {
        $analista = User::factory()->create();
        $area = Area::factory()->create();
        $analista->areas()->attach($area);
        $token = auth()->login($analista);

        $response = $this->deleteJson(
            route('analista.dissociate', ['analistaId' => $analista->id, 'areaId' => $area->id]),
            [],
            ['Authorization' => 'Bearer ' . $token]
        );

        $this->assertFalse($analista->areas->contains($area));
    }

    public function test_duplicate_association()
    {
        $analista = User::factory()->create();
        $token = auth()->login($analista);
        $area = Area::factory()->create();

        $analista->areas()->attach($area);

        $response = $this->postJson(
            route('analista.associate', ['analistaId' => $analista->id, 'areaId' => $area->id]),
            [],
            ['Authorization' => 'Bearer ' . $token]
        );

        $response->assertStatus(403);
    }

    // casos de usuario nao autenticado

    /** @test */
    public function test_guest_cannot_associate_analista_with_area()
    {
        $analista = User::factory()->create(['type' => 'suporte']);
        $area = Area::factory()->create();

        $response = $this->postJson(
            route('analista.associate', ['analistaId' => $analista->id, 'areaId' => $area->id])
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function test_guest_cannot_dissociate_analista_from_area()
    {
        $analista = User::factory()->create();
        $area = Area::factory()->create();

        $response = $this->deleteJson(
            route('analista.dissociate', ['analistaId' => $analista->id, 'areaId' => $area->id])
        );

        $response->assertStatus(401);
    }
}
