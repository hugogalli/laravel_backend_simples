<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AreaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_get_list_of_areas()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->getJson(route('areas.index'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'areas' => [
                    '*' => ['id', 'title', 'description', 'created_at', 'updated_at'],
                ],
            ]);
    }

    /** @test */
    /** @test */
    public function test_user_can_create_area()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $areaData = [
            'title' => 'Test Area',
        ];

        $response = $this->postJson(route('area.store'), $areaData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'area created successfully',
                'area' => [
                    'title' => 'Test Area',
                ],
            ]);

        $this->assertDatabaseHas('areas', [
            'title' => 'Test Area',
        ]);
    }

    /** @test */
    public function test_user_can_show_area()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $area = Area::factory()->create();

        $response = $this->getJson(route('area.show', ['id' => $area->id]), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'area' => [
                    'title' => $area->title,
                ],
            ]);
    }

    /** @test */
    public function test_user_can_update_area()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $area = Area::factory()->create();

        $updatedData = [
            'title' => 'Updated Area',
        ];

        $response = $this->putJson(route('area.update', ['id' => $area->id]), $updatedData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Area updated successfully',
                'area' => [
                    'title' => 'Updated Area',
                ],
            ]);

        $this->assertDatabaseHas('areas', [
            'id' => $area->id,
            'title' => 'Updated Area',
        ]);
    }

    /** @test */
    public function test_user_can_delete_area()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $area = Area::factory()->create();

        $response = $this->deleteJson(route('area.destroy', ['id' => $area->id]));
        $response->assertStatus(200);

        $this->assertDatabaseMissing('areas', ['id' => $area->id]);
    }

    // Testes para usuarios sem JWT TOKEN

    public function test_guest_cannot_get_list_of_areas()
    {
        $response = $this->getJson(route('areas.index'));

        $response->assertStatus(401);
    }
    
    public function test_guest_cannot_create_area()
    {
        $areaData = [
            'title' => 'New Area',
        ];

        $response = $this->postJson(route('area.store'), $areaData);

        $response->assertStatus(401);
    }

    public function test_guest_cannot_show_area()
    {
        $area = Area::factory()->create();

        $response = $this->getJson(route('area.show', ['id' => $area->id]));

        $response->assertStatus(401);
    }

    public function test_guest_cannot_update_area()
    {
        $area = Area::factory()->create();

        $response = $this->putJson(route('area.update', ['id' => $area->id]), [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);

        $response->assertStatus(401);
    }

    public function test_guest_cannot_delete_area()
    {
        $area = Area::factory()->create();

        $response = $this->deleteJson(route('area.destroy', ['id' => $area->id]));

        $response->assertStatus(401);
    }
}
