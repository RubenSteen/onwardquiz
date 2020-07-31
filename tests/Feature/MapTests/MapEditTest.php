<?php

namespace Tests\Feature\MapTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Map;

class MapEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_edit_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn(['super_admin' => false, 'editor' => false]);

        $this->get(route('map.edit', $map->id))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_edit_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->get(route('map.edit', $map->id))->assertStatus(200);
    }

    /** @test */
    public function a_editor_can_edit_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->get(route('map.edit', $map->id))->assertStatus(200);
    }
}
