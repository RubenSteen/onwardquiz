<?php

namespace Tests\Feature\MapTests;

use App\Map;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_view_the_edit_page()
    {
        $map = $this->createMap();

        $this->signIn(['super_admin' => false, 'editor' => false]);

        $this->get(route('map.edit', $map->id))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_view_the_edit_page()
    {
        $map = $this->createMap();

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $response = $this->get(route('map.edit', $map->id));

        $this->assertEquals($response->props()['map']['name'], $map->name);

        $response->assertStatus(200);
    }

    /** @test */
    public function a_editor_can_view_the_edit_page()
    {
        $map = $this->createMap();

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $response = $this->get(route('map.edit', $map->id));

        $this->assertEquals($response->props()['map']['name'], $map->name);

        $response->assertStatus(200);
    }
}
