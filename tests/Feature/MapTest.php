<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Map;
use App\User;
use App\Http\Requests\Map\MapCreate;
use App\Http\Requests\Map\MapUpdate;

class MapTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /*
     * Index tests
     */

    /** @test */
    public function a_authenticated_user_cannot_view_the_index()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $this->get(route('map.index'))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_view_the_index()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->get(route('map.index'))->assertStatus(200);
    }

    /** @test */
    public function a_editor_can_view_the_index()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->get(route('map.index'))->assertStatus(200);
    }

    /*
     * Create tests
     */

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

        $this->assertEquals(session('errors')->get('name')[0],"The name field is required.");
    }

    /** @test */
    public function name_is_unique_while_creating_a_map()
    {
        $this->signIn(['editor' => true]);

        // Data that will be send
        $data = factory(Map::class)->raw(['name' => 'testing']);

        $this->post(route('map.store'), $data)->isSuccessful();
        $this->post(route('map.store'), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('name')[0],"The name has already been taken.");
    }

    /*
     * Show tests
     */



    /*
     * Edit tests
     */

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

    /*
     * Update tests
     */

    /** @test */
    public function a_authenticated_user_cannot_update_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn();

        $this->patch(route('map.update', $map->id), ['name' => 'Some other name'])->assertForbidden();

        $this->assertDatabaseHas((new Map)->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function a_superadmin_can_update_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw(['name' => 'Some Other name']));

        $this->assertDatabaseMissing((new Map)->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function a_editor_can_update_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw(['name' => 'Some Other name']));

        $this->assertDatabaseMissing((new Map)->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function name_is_required_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        // Data that will be send
        $data = [
            'name' => '',
        ];

        $map = factory(Map::class)->create();

        $this->patch(route('map.update', $map->id), $data)->assertSessionHasErrors();
        $this->assertEquals(session('errors')->get('name')[0],"The name field is required.");
    }

    /** @test */
    public function name_is_unique_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $existingMap = factory(Map::class)->create();
        $updateMap = factory(Map::class)->create();

        // Data that will be send
        $data = [
            'name' => $existingMap->name,
        ];

        $this->patch(route('map.update', $updateMap->id), $data)->assertSessionHasErrors();
        $this->assertEquals(session('errors')->get('name')[0],"The name has already been taken.");
    }

    /** @test */
    public function published_is_required_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        // Data that will be send
        $data = [
            'published' => '',
        ];

        $map = factory(Map::class)->create();

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('published')[0],"The published field is required.");
    }

//    /** @test */
//    public function published_is_only_possible_with_a_minimum_of_4_questions_published_while_updating_a_map()
//    {
//        $this->signIn(['editor' => true]);
//
//        // Data that will be send
//        $data = [
//            'published' => 'true',
//        ];
//
//        $map = factory(Map::class)->create();
//
//        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->assertSessionHasErrors();
//
//        $this->assertEquals(session('errors')->get('published')[0],"The published field is required.");
//    }

    /*
     * Delete tests
     */

    /** @test */
    public function a_authenticated_user_cannot_delete_a_map()
    {
        $map = factory(Map::class)->create();

        $this->assertCount(1, Map::all());

        $this->signIn();

        $this->delete(route('map.destroy', $map->id))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_delete_a_map()
    {
        $map = factory(Map::class)->create();

        $this->assertCount(1, Map::all());

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
    }

    /** @test */
    public function a_editor_can_delete_a_map()
    {
        $map = factory(Map::class)->create();

        $this->assertCount(1, Map::all());

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
    }

    /*
     * Restore tests
     */
}
