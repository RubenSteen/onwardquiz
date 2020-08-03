<?php

namespace Tests\Feature\QuestionTests;

use App\Map;
use App\Question;
use App\QuestionPicture;
use App\Upload;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionRestoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_restore_a_question()
    {
        $question = $this->createQuestion(['deleted_at' => Carbon::now()]);

        $this->assertCount(1, Question::onlyTrashed()->get());

        $this->signIn();

        $this->put(route('question.restore', ['map' => $question->map->id, 'question_id' => $question->id]))->assertForbidden();

        $this->assertCount(1, Question::onlyTrashed()->get());
    }

    /** @test */
    public function a_superadmin_can_restore_a_question()
    {
        $this->withoutExceptionHandling();
        $question = $this->createQuestion(['deleted_at' => Carbon::now()]);

        $this->assertCount(1, Question::onlyTrashed()->get());

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->put(route('question.restore', ['map' => $question->map->id, 'question_id' => $question->id]))
            ->assertRedirect(route('question.edit', ['map' => $question->map->id, 'question' => $question->id]));

        $this->assertCount(1, Question::all());
        $this->assertEquals($question->fresh()->deleted_at, null);
    }

    /** @test */
    public function a_editor_can_restore_a_question()
    {
        $question = $this->createQuestion(['deleted_at' => Carbon::now()]);

        $this->assertCount(1, Question::onlyTrashed()->get());

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->put(route('question.restore', ['map' => $question->map->id, 'question_id' => $question->id]))
            ->assertRedirect(route('question.edit', ['map' => $question->map->id, 'question' => $question->id]));

        $this->assertCount(1, Question::all());
        $this->assertEquals($question->fresh()->deleted_at, null);
    }

    /** @test */
    public function cannot_restore_a_question_when_the_map_is_not_the_given_map()
    {
        $question = $this->createQuestion(['deleted_at' => Carbon::now()]); // Parent map is ID 1 and the question is linked to it.

        $fakeMap = $this->createMap(); // fakeMap is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::onlyTrashed()->get());

        $this->signIn(['editor' => true]);

        $this->put(route('question.restore', ['map' => $fakeMap->id, 'question_id' => $question->id]))
            ->assertRedirect(route('map.edit', $fakeMap->id));
    }

    private $max_time_before_out_of_range_for_the_restore_in_seconds = 5;

    /** @test */
    public function pictures_will_get_restored_if_the_picture_deleted_at_timestamp_is_within_x_seconds_of_question_deleted_at_timestamp_when_a_question_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeQuestion = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeOutOfRange = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds + 1)); // Is just out of range to get restored
        $timeWithinRange = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds - 1)); // Is a second within range so it will get restored
        $timeExactlyWithinRange = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds($addSeconds); // Is exactly on the time so it still will get restored

        $question = $this->createQuestion(['deleted_at' => $timeQuestion]);

        $this->createQuestionPicture(['question_id' => $question->id, 'difficulty' => 6, 'deleted_at' => $timeOutOfRange]); // Is just out of range to get restored
        $this->createQuestionPicture(['question_id' => $question->id, 'difficulty' => 4, 'deleted_at' => $timeWithinRange]); // Is a second within range so it will get restored
        $this->createQuestionPicture(['question_id' => $question->id, 'difficulty' => 5, 'deleted_at' => $timeExactlyWithinRange]); // Is exactly on the time so it still will get restored

        $this->assertCount(0, Question::all());
        $this->assertCount(0, QuestionPicture::all());

        $this->signIn(['editor' => true]);

        $this->put(route('question.restore', ['map' => $question->map->id, 'question_id' => $question->id]))->isSuccessful();

        $this->assertCount(1, Question::all());
        $this->assertCount(3, QuestionPicture::withTrashed()->get());
        $this->assertDatabaseHas((new QuestionPicture)->getTable(), ['difficulty' => 4, 'deleted_at' => null]);
        $this->assertDatabaseHas((new QuestionPicture)->getTable(), ['difficulty' => 5, 'deleted_at' => null]);
        $this->assertDatabaseHas((new QuestionPicture)->getTable(), ['difficulty' => 6, 'deleted_at' => $timeOutOfRange]);
    }

    /** @test */
    public function will_not_throw_a_status_500_if_there_are_no_questions_pictured_to_restore_with_the_specified_question_when_a_question_is_restored()
    {
        $question = $this->createQuestion(['deleted_at' => Carbon::now()]);

        $this->assertCount(0, Question::all());
        $this->assertCount(0, QuestionPicture::withTrashed()->get());

        $this->signIn(['editor' => true]);

        $this->put(route('question.restore', ['map' => $question->map->id, 'question_id' => $question->id]))->isSuccessful();

        $this->assertCount(1, Question::all());
        $this->assertCount(0, QuestionPicture::withTrashed()->get());
    }

    /** @test */
    public function the_first_template_will_get_restored_if_the_template_deleted_at_timestamp_is_within_x_seconds_of_question_deleted_at_timestamp_when_a_question_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeMap = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeOldest = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds - 2)); // Has been deleted before the $timeNewest timestamp
        $timeNewest = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds - 1)); // The newest has been deleted later than the oldest

        $question = $this->createQuestion(['deleted_at' => $timeMap], 1, false); // A map with a Older template but still in range for restore

        factory(Upload::class)->create(['deleted_at' => $timeOldest, 'uploadable_id' => $question->id, 'uploadable_type' => 'App\Question', 'name' => 'oldest.jpg']); // Older template but still in range for restore
        factory(Upload::class)->create(['deleted_at' => $timeNewest, 'uploadable_id' => $question->id, 'uploadable_type' => 'App\Question', 'name' => 'newest.jpg']); // Most recent template that will be restored

        $this->assertCount(0, Question::all());
        $this->assertCount(0, Upload::all());

        $this->signIn(['editor' => true]);

        $this->put(route('question.restore', ['map' => $question->map->id, 'question_id' => $question->id]))->isSuccessful();

        $this->assertCount(1, Question::all());
        $this->assertCount(2, Upload::withTrashed()->get());
        $this->assertDatabaseHas((new Upload)->getTable(), ['name' => 'newest.jpg', 'deleted_at' => null]);
        $this->assertDatabaseHas((new Upload)->getTable(), ['name' => 'oldest.jpg', 'deleted_at' => $timeOldest]);
    }

    /** @test */
    public function no_template_will_get_restored_if_the_template_deleted_at_timestamp_is_not_within_x_seconds_of_question_deleted_at_timestamp_when_a_question_is_restored()
    {
        $currentTime = Carbon::now();
        $addSeconds = $this->max_time_before_out_of_range_for_the_restore_in_seconds;

        $timeQuestion = Carbon::createFromTimestamp($currentTime->timestamp);
        $timeTemplate = Carbon::createFromTimestamp($currentTime->timestamp)->addSeconds(($addSeconds + 1)); // Has been deleted but is outside the max deleted_at time

        $question = $this->createQuestion(['deleted_at' => $timeQuestion], 1, false);

        factory(Upload::class)->create(['deleted_at' => $timeTemplate, 'uploadable_id' => $question->id, 'uploadable_type' => 'App\Question']);

        $this->assertCount(0, Question::all());
        $this->assertCount(0, Upload::all());

        $this->signIn(['editor' => true]);

        $this->put(route('question.restore', ['map' => $question->map->id, 'question_id' => $question->id]))->isSuccessful();

        $this->assertCount(1, Question::all());
        $this->assertCount(0, Upload::all());
    }
}