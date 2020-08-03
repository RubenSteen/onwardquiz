<?php

namespace Tests\Feature\MapTests;

use App\Question;
use App\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Map;

class MapDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_delete_a_map()
    {
        $map = $this->createMap();

        $this->assertCount(1, Map::all());

        $this->signIn();

        $this->delete(route('map.destroy', $map->id))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_delete_a_map()
    {
        $map = $this->createMap();

        $this->assertCount(1, Map::all());

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->delete(route('map.destroy', $map->id))
            ->assertRedirect(route('map.index'));

        $this->assertCount(0, Map::all());
    }

    /** @test */
    public function a_editor_can_delete_a_map()
    {
        $map = $this->createMap();

        $this->assertCount(1, Map::all());

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->delete(route('map.destroy', $map->id))
            ->assertRedirect(route('map.index'));

        $this->assertCount(0, Map::all());
    }

    /** @test */
    public function questions_get_deleted_when_a_map_is_deleted()
    {
        $map = $this->createMap();

        $this->createQuestion(['map_id' => $map->id], 10);

        $this->assertCount(1, Map::all());
        $this->assertCount(10, Question::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Question::all());
    }

    /** @test */
    public function does_not_throw_a_server_error_when_no_questions_exist_when_a_map_is_deleted()
    {
        $map = $this->createMap();

        $this->assertCount(1, Map::all());
        $this->assertCount(0, Question::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Question::all());
    }

    /** @test */
    public function template_gets_deleted_when_a_map_is_deleted()
    {
        $map = $this->createMap();

        $this->assertCount(1, Map::all());
        $this->assertCount(1, Upload::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Upload::all());
    }

    /** @test */
    public function does_not_throw_a_server_error_when_no_template_exist_when_a_map_is_deleted()
    {
        $map = $this->createMap([], 1, false);

        $this->assertCount(1, Map::all());
        $this->assertCount(0, Upload::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Upload::all());
    }
}