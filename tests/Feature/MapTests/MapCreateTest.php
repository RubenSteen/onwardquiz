<?php

namespace Tests\Feature\MapTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Map;

class MapCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_create_a_map()
    {
        $this->signIn();

        $this->post(route('map.store'), factory(Map::class)->raw())->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_create_a_map()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->post(route('map.store'), factory(Map::class)->raw())->isSuccessful();

        $this->assertCount(1, Map::all());
    }

    /** @test */
    public function a_editor_can_create_a_map()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->post(route('map.store'), factory(Map::class)->raw())->isSuccessful();

        $this->assertCount(1, Map::all());
    }

    /** @test */
    public function name_is_required_while_creating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw(['name' => '']);

        $this->post(route('map.store'), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('name')[0], "The name field is required.");
    }

    /** @test */
    public function name_is_unique_while_creating_a_map()
    {
        $this->signIn(['editor' => true]);

        // Data that will be send
        $data = factory(Map::class)->raw(['name' => 'testing']);

        $this->post(route('map.store'), $data)->isSuccessful();
        $this->post(route('map.store'), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('name')[0], "The name has already been taken.");
    }
}
