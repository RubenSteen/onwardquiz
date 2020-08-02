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

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
    }

    /** @test */
    public function a_editor_can_delete_a_map()
    {
        $map = $this->createMap();

        $this->assertCount(1, Map::all());

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
    }

    /** @test */
    public function questions_get_deleted_when_a_map_is_deleted()
    {
        $map = $this->createMap();

        $map->questions()->create(factory(Question::class)->raw(['map_id' => $map->id]));

        $this->assertCount(1, Map::all());
        $this->assertCount(1, Question::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Question::all());
    }

    /** @test */
    public function template_gets_deleted_when_a_map_is_deleted()
    {
        $map = $this->createMap();

        $map->template()->create(factory(Upload::class)->raw());

        $this->assertCount(1, Map::all());
        $this->assertCount(1, Upload::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Upload::all());
    }
}