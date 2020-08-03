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
        $map = $this->createMap(['deleted_at' => Carbon::now()]);

        $this->assertCount(0, Map::all());

        $this->signIn();

        $this->put(route('map.restore', $map->id))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_restore_a_map()
    {
        $map = $this->createMap(['deleted_at' => Carbon::now()]);

        $this->assertCount(0, Map::all());

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
    }

    /** @test */
    public function a_editor_can_restore_a_map()
    {
        $map = $this->createMap(['deleted_at' => Carbon::now()]);

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

        $map = $this->createMap(['deleted_at' => $timeMap]);

        $this->createQuestion(['map_id' => $map->id, 'callout' => 'outofrange', 'deleted_at' => $timeOutOfRange]); // Is just out of range to get restored
        $this->createQuestion(['map_id' => $map->id, 'callout' => 'secondwithinrange', 'deleted_at' => $timeWithinRange]); // Is a second within range so it will get restored
        $this->createQuestion(['map_id' => $map->id, 'callout' => 'exactlyontime', 'deleted_at' => $timeExactlyWithinRange]); // Is exactly on the time so it still will get restored

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Question::all());

        $this->signIn(['editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
        $this->assertCount(3, Question::withTrashed()->get());
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => 'secondwithinrange', 'deleted_at' => null]);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => 'exactlyontime', 'deleted_at' => null]);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => 'outofrange', 'deleted_at' => $timeOutOfRange]);
    }

    /** @test */
    public function will_not_throw_a_status_500_if_there_are_no_questions_to_restore_with_the_specified_map_when_a_map_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeMap = Carbon::createFromTimestamp($currentTime->timestamp);

        $map = $this->createMap(['deleted_at' => $timeMap]);

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Question::withTrashed()->get());

        $this->signIn(['editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
        $this->assertCount(0, Question::withTrashed()->get());
    }

    /** @test */
    public function the_first_template_will_get_restored_if_the_template_deleted_at_timestamp_is_within_x_seconds_of_map_deleted_at_timestamp_when_a_map_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeMap = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeOldest = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds - 2)); // Has been deleted before the $timeNewest timestamp
        $timeNewest = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds - 1)); // The newest has been deleted later than the oldest

        $map = $this->createMap(['deleted_at' => $timeMap], 1, false); // A map with a Older template but still in range for restore

        $oldestTemplate = factory(Upload::class)->create(['deleted_at' => $timeOldest, 'uploadable_id' => $map->id, 'uploadable_type' => 'App\Map', 'name' => 'oldest.jpg']); // Older template but still in range for restore
        factory(Upload::class)->create(['deleted_at' => $timeNewest, 'uploadable_id' => $map->id, 'uploadable_type' => 'App\Map', 'name' => 'newest.jpg']); // Most recent template that will be restored

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Upload::all());

        $this->signIn(['editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
        $this->assertCount(2, Upload::withTrashed()->get());
        $this->assertDatabaseHas((new Upload)->getTable(), ['name' => 'newest.jpg', 'deleted_at' => null]);
        $this->assertDatabaseHas((new Upload)->getTable(), ['name' => 'oldest.jpg', 'deleted_at' => $timeOldest]);
    }

    /** @test */
    public function no_template_will_get_restored_if_they_question_deleted_at_timestamp_is_not_within_x_seconds_of_map_deleted_at_timestamp_when_a_map_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeMap = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeQuestion = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds + 1)); // Has been deleted but is outside the max deleted_at time

        $map = $this->createMap(['deleted_at' => $timeMap], 1, false);

        factory(Upload::class)->create(['deleted_at' => $timeQuestion, 'uploadable_id' => $map->id, 'uploadable_type' => 'App\Map']);

        $this->assertCount(0, Map::all());
        $this->assertCount(0, Upload::all());

        $this->signIn(['editor' => true]);

        $this->put(route('map.restore', $map->id))->isSuccessful();

        $this->assertCount(1, Map::all());
        $this->assertCount(0, Upload::all());
    }
}
