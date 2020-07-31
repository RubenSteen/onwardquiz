<?php

namespace Tests\Feature\MapTests;

use App\Question;
use App\Upload;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Map;

class MapRestoreTest extends TestCase
{
    use RefreshDatabase;

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
