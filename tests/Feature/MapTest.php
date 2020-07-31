<?php

namespace Tests\Feature;

use App\Question;
use App\Upload;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
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

        $this->assertDatabaseHas((new Map())->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function a_superadmin_can_update_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw(['name' => 'Some Other name']));

        $this->assertDatabaseMissing((new Map())->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function a_editor_can_update_a_map()
    {
        $map = factory(Map::class)->create();

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw(['name' => 'Some Other name']));

        $this->assertDatabaseMissing((new Map())->getTable(), ['name' => $map->name]);
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

        $this->assertEquals(session('errors')->get('name')[0], "The name field is required.");
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

        $this->assertEquals(session('errors')->get('name')[0], "The name has already been taken.");
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

        $this->assertEquals(session('errors')->get('published')[0], "The published field is required.");
    }

    private $min_questions_published = 4;

    /** @test */
    public function cannot_publish_a_map_if_it_does_not_have_x_questions_published_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = [
            'published' => true,
        ];

        $map = factory(Map::class)->create();

        for ($i = 0; $i < $this->min_questions_published; $i++) {
            $map->questions()->create(factory(Question::class)->raw(['published' => false, 'map_id' => $map->id]));
        }

        $this->assertCount($this->min_questions_published, Question::all());

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('published')[0],"Cannot be published, the map requires to have a minimum of 4 questions published");
    }

    /** @test */
    public function can_publish_a_map_if_it_does_have_x_questions_published_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = [
            'published' => true,
        ];

        $map = factory(Map::class)->create();

        for ($i = 0; $i < $this->min_questions_published; $i++) {
            $map->questions()->create(factory(Question::class)->raw(['published' => true, 'map_id' => $map->id]));
        }

        $this->assertCount($this->min_questions_published, Question::where('published', true)->get());

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->isSuccessful();

        $this->assertDatabaseHas((new Map())->getTable(), ['published' => true]);
    }

    /** @test */
    public function description_is_nullable_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = [
            'description' => '',
        ];

        $map = factory(Map::class)->create(['description' => 'Something']);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->isSuccessful();

        $this->assertDatabaseHas((new Map())->getTable(), ['description' => null]);
    }

    /** @test */
    public function description_can_be_set_to_a_string_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = [
            'description' => 'Some other text',
        ];

        $map = factory(Map::class)->create(['description' => 'Something']);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->isSuccessful();

        $this->assertDatabaseMissing((new Map())->getTable(), ['description' => $map->description]);
    }

    /** @test */
    public function image_is_nullable_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = array_merge(factory(Map::class)->raw(), ['image' => '']);

        $map = factory(Map::class)->create();

        $this->patch(route('map.update', $map->id), $data)->isSuccessful();

        $this->assertEquals(0, $map->image()->count());
        $this->assertDatabaseCount((new Map())->getTable(), 1);
    }

    /** @test */
    public function image_must_be_a_type_of_image_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = array_merge(factory(Map::class)->raw(), ['image' => 'some text']);

        $map = factory(Map::class)->create();

        $this->patch(route('map.update', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('image')[0],"The image must be an image.");
    }

    private $map_max_image_size = 15000;

    /** @test */
    public function a_image_can_be_uploaded_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw();

        $data['image'] = UploadedFile::fake()->image('avatar.jpg')->size($this->map_max_image_size);

        $map = factory(Map::class)->create();

        $this->patch(route('map.update', $map->id), $data)->isSuccessful();

        $this->assertEquals(1, $map->image()->count());
        $this->assertDatabaseCount((new Map())->getTable(), 1);
    }

    /** @test */
    public function delete_old_image_when_a_user_adds_a_new_one_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw();

        $data['image'] = UploadedFile::fake()->image('avatar.jpg')->size($this->map_max_image_size);

        $map = factory(Map::class)->create();

        $this->patch(route('map.update', $map->id), $data)->isSuccessful();
        $this->patch(route('map.update', $map->id), $data)->isSuccessful();

        $this->assertEquals(1, $map->image()->count());
        $this->assertEquals(2, $map->image()->withTrashed()->count());
        $this->assertDatabaseCount((new Map())->getTable(), 1);
    }

    /** @test */
    public function image_size_cannot_be_greater_then_x_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw();

        $data['image'] = UploadedFile::fake()->image('avatar.jpg')->size(($this->map_max_image_size + 1));

        $map = factory(Map::class)->create();

        $this->patch(route('map.update', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('image')[0],"The image may not be greater than 15000 kilobytes.");
    }

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

    /** @test */
    public function questions_get_deleted_when_a_map_is_deleted()
    {
        $map = factory(Map::class)->create();

        $map->questions()->create(factory(Question::class)->raw(['map_id' => $map->id]));

        $this->assertCount(1, Map::all());
        $this->assertCount(1, Question::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Question::all());
    }

    /** @test */
    public function images_get_deleted_when_a_map_is_deleted()
    {
        $map = factory(Map::class)->create();

        $map->image()->create(factory(Upload::class)->raw());

        $this->assertCount(1, Map::all());
        $this->assertCount(1, Upload::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('map.destroy', $map->id))->isSuccessful();

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Upload::all());
    }

    /*
     * Restore tests
     */

    /** @test */
    public function a_authenticated_user_cannot_restore_a_map()
    {
        $map = factory(Map::class)->create(['deleted_at' => Carbon::now()]);

        $this->assertCount(0, Map::all());

        $this->signIn();

        $this->put(route('map.restore', $map->id))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_restore_a_map()
    {
        $this->withoutExceptionHandling();

        $map = factory(Map::class)->create(['deleted_at' => Carbon::now()]);

        $this->assertCount(0, Map::all());

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
    }

    /** @test */
    public function a_editor_can_restore_a_map()
    {
        $map = factory(Map::class)->create(['deleted_at' => Carbon::now()]);

        $this->assertCount(0, Map::all());

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
    }

    private $max_time_before_out_of_range_for_the_restore_in_seconds = 5;

    /** @test */
    public function questions_will_get_restored_if_the_question_deleted_at_timestamp_is_within_x_seconds_of_map_deleted_at_timestamp_when_a_map_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeMap = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeOutOfRange = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds + 1)); // Is just out of range to get restored
        $timeWithinRange = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds - 1)); // Is a second within range so it will get restored
        $timeExactlyWithinRange = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds($addSeconds); // Is exactly on the time so it still will get restored

        $map = factory(Map::class)->create(['deleted_at' => $timeMap]);

        factory(Question::class)->create(['deleted_at' => $timeOutOfRange, 'map_id' => $map->id, 'callout' => 'outofrange']); // Is just out of range to get restored
        factory(Question::class)->create(['deleted_at' => $timeWithinRange, 'map_id' => $map->id, 'callout' => 'secondwithinrange']); // Is a second within range so it will get restored
        factory(Question::class)->create(['deleted_at' => $timeExactlyWithinRange, 'map_id' => $map->id, 'callout' => 'exactlyontime']); // Is exactly on the time so it still will get restored

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Question::all());

        $this->signIn(['editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
        $this->assertCount(2, Question::all());
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => 'secondwithinrange', 'deleted_at' => null]);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => 'exactlyontime', 'deleted_at' => null]);
    }

    /** @test */
    public function no_questions_will_get_restored_if_the_question_deleted_at_timestamp_is_not_within_x_seconds_of_map_deleted_at_timestamp_when_a_map_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeMap = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeOutOfRange = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds + 1)); // Is just out of range to get restored

        $map = factory(Map::class)->create(['deleted_at' => $timeMap]);

        factory(Question::class)->create(['deleted_at' => $timeOutOfRange, 'map_id' => $map->id]); // Is just out of range to get restored

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Question::all());

        $this->signIn(['editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
        $this->assertCount(0, Question::all());
    }

    /** @test */
    public function the_first_image_will_get_restored_if_the_image_deleted_at_timestamp_is_within_x_seconds_of_map_deleted_at_timestamp_when_a_map_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeMap = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeOldest = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds - 2)); // Has been deleted before the $timeNewest timestamp
        $timeNewest = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds - 1)); // The newest has been deleted later than the oldest

        $map = factory(Map::class)->create(['deleted_at' => $timeMap]);

        factory(Upload::class)->create(['deleted_at' => $timeOldest, 'uploadable_id' => $map->id, 'uploadable_type' => 'App\Map', 'name' => 'oldest.jpg']); // Older image but still in range for restore
        factory(Upload::class)->create(['deleted_at' => $timeNewest, 'uploadable_id' => $map->id, 'uploadable_type' => 'App\Map', 'name' => 'newest.jpg']); // Most recent image that will be restored

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Upload::all());

        $this->signIn(['editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
        $this->assertCount(1, Upload::all());
        $this->assertDatabaseHas((new Upload)->getTable(), ['name' => 'newest.jpg', 'deleted_at' => null]);
    }

    /** @test */
    public function no_image_will_get_restored_if_they_question_deleted_at_timestamp_is_not_within_x_seconds_of_map_deleted_at_timestamp_when_a_map_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeMap = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeQuestion = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds + 1)); // Has been deleted but is outside the max deleted_at time

        $map = factory(Map::class)->create(['deleted_at' => $timeMap]);

        factory(Upload::class)->create(['deleted_at' => $timeQuestion, 'uploadable_id' => $map->id, 'uploadable_type' => 'App\Map']);

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Upload::all());

        $this->signIn(['editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
        $this->assertCount(0, Upload::all());
    }
}
