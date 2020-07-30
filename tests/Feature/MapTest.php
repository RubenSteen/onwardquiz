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
        $this->withoutExceptionHandling();
        
        $this->signIn();

        $this->expectExceptionMessage('This action is unauthorized.');

        $this->post(route('map.store'), factory(Map::class)->raw());
    }

    /** @test */
    public function a_superadmin_can_create_a_map()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->post(route('map.store'), factory(Map::class)->raw());

        $this->assertCount(1, Map::all());
    }

    /** @test */
    public function a_editor_can_create_a_map()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->post(route('map.store'), factory(Map::class)->raw());

        $this->assertCount(1, Map::all());
    }

        /** @test */
    public function a_map_name_is_required()
    {
        // Expected error messages
        $errors = [
            'The name field is required.'
        ];

        $this->signIn(['editor' => true]);

        // Data that will be send
        $data = [
            'name' => ''
        ];

        // Checks if the data array contains everything in the validation rules
        // If a field can be present sometimes you can higher the expectedCount.
        $this->assertCount(0, array_diff(array_keys(MapCreate::getRules()), array_keys($data)));

        $newMap = factory(Map::class)->raw($data);

        $this->post(route('map.store'), $newMap)->assertSessionHasErrors();

        foreach(session('errors')->all() as $error) {
            $this->assertContains($error, $errors);
        }
    }

        /** @test */
    public function a_map_does_not_accept_values_that_are_not_in_the_validation()
    {
        $this->signIn(['editor' => true]);

        $newMap = factory(Map::class)->raw(['published' => true]);

        $this->post(route('map.store'), $newMap);

        $this->assertDatabaseHas((new Map())->getTable(), ['published' => false]);
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

        $this->withoutExceptionHandling();

        $this->signIn();

        $this->expectExceptionMessage('This action is unauthorized.');

        $this->patch(route('map.update', $map->id), ['name' => 'Some other name']);
    }

    /** @test */
    public function a_superadmin_can_update_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->patch(route('map.update', $map->id), ['name' => 'Some other name']);

        $this->assertCount(1, Map::all());

        $this->assertDatabaseMissing((new Map)->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function a_editor_can_update_a_map()
    {
        $map = factory(Map::class)->create();

        $this->withoutExceptionHandling();

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->patch(route('map.update', $map->id), ['name' => 'Some other name']);

        $this->assertCount(1, Map::all());

        $this->assertDatabaseMissing((new Map)->getTable(), ['name' => $map->name]);
    }

//    /** @test */
//    public function a_map_requires_a_name()
//    {
//        $this->signIn(['editor' => true]);
//
//        $newMap = factory(Map::class)->raw(['name' => '']);
//
//        $this->post(route('map.store'), $newMap)->assertSessionHasErrors('name');
//    }
//
//    /** @test */
//    public function a_map_does_not_accept_values_that_are_not_in_the_validation()
//    {
//        $this->signIn(['editor' => true]);
//
//        $newMap = factory(Map::class)->raw(['published' => true]);
//
//        $this->post(route('map.store'), $newMap);
//
//        $this->assertDatabaseHas((new Map())->getTable(), ['published' => false]);
//    }

    /*
     * Delete tests
     */

    /** @test */
    public function a_authenticated_user_cannot_delete_a_map()
    {
        $map = factory(Map::class)->create();

        $this->assertCount(1, Map::all());

        $this->withoutExceptionHandling();

        $this->signIn();

        $this->expectExceptionMessage('This action is unauthorized.');

        $this->delete(route('map.destroy', $map->id));
    }

    /** @test */
    public function a_superadmin_can_delete_a_map()
    {
        $map = factory(Map::class)->create();

        $this->assertCount(1, Map::all());

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->delete(route('map.destroy', $map->id));

        $this->assertCount(0, Map::all());
    }

    /** @test */
    public function a_editor_can_delete_a_map()
    {
        $map = factory(Map::class)->create();

        $this->assertCount(1, Map::all());

        $this->withoutExceptionHandling();

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->delete(route('map.destroy', $map->id));

        $this->assertCount(0, Map::all());
    }

    /*
     * Restore tests
     */
}
