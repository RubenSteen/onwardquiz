<?php

namespace Tests\Feature\QuestionTests\QuestionPictureTests;

use App\Map;
use App\Question;
use App\QuestionPicture;
use App\Upload;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionPictureRestoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_restore_a_question_picture()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $questionPicture = $this->createQuestionPicture(['deleted_at' => Carbon::now()]);

        $this->put(route('question.restore.picture', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id, 'picture_id' => $questionPicture->id]))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_restore_a_question_picture()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $questionPicture = $this->createQuestionPicture(['deleted_at' => Carbon::now()]);

        $this->assertCount(0, QuestionPicture::all());

        $this->put(route('question.restore.picture', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id, 'picture_id' => $questionPicture->id]))
            ->assertRedirect(route('question.edit', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id]));

        $this->assertCount(1, QuestionPicture::withTrashed()->get());
        $this->assertEquals($questionPicture->fresh()->deleted_at, null);
    }

    /** @test */
    public function a_editor_can_restore_a_question_picture()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $questionPicture = $this->createQuestionPicture(['deleted_at' => Carbon::now()]);

        $this->assertCount(0, QuestionPicture::all());

        $this->put(route('question.restore.picture', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id, 'picture_id' => $questionPicture->id]))
            ->assertRedirect(route('question.edit', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id]));

        $this->assertCount(1, QuestionPicture::withTrashed()->get());
        $this->assertEquals($questionPicture->fresh()->deleted_at, null);
    }

    /** @test */
    public function cannot_restore_a_question_picture_when_the_map_is_not_the_parent_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $questionPicture = $this->createQuestionPicture(['deleted_at' => Carbon::now()]); // Parent map is ID 1 and the question is linked to it with ID 1 and the question picture is linked to that.

        $fakeMap = $this->createMap(); // fakeMap is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());
        $this->assertCount(0, QuestionPicture::all());

        $this->expectException(ModelNotFoundException::class);

        $this->put(route('question.restore.picture', ['map' => $fakeMap->id, 'question' => $questionPicture->question->id, 'picture', $questionPicture->id]));
    }

    /** @test */
    public function cannot_restore_a_question_picture_when_the_question_picture_is_not_the_child_of_the_given_question()
    {
        $this->signIn(['editor' => true]);

        $questionPicture = $this->createQuestionPicture(['deleted_at' => Carbon::now()]); // Parent map is ID 1 and the question is linked to it with ID 1 and the question picture is linked to that.

        $fakeQuestionPicture = $this->createQuestionPicture(['deleted_at' => Carbon::now()]); // $fakeQuestionPicture is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(2, Question::all());
        $this->assertCount(0, QuestionPicture::all());

        $this->put(route('question.restore.picture', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->id, 'picture', $fakeQuestionPicture->id]))
            ->assertRedirect(route('question.edit', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id]));

        $this->assertNotEquals($fakeQuestionPicture->fresh()->deleted_at, null);
    }

    /** @test */
    public function image_will_get_restored__when_a_question_picture_is_restored()
    {
        $this->signIn(['editor' => true]);

        $questionPicture = $this->createQuestionPicture(['deleted_at' => Carbon::now()], 1, false);

        factory(Upload::class)->create(['deleted_at' => Carbon::now(), 'uploadable_id' => $questionPicture->id, 'uploadable_type' => 'App\QuestionPicture']);

        $this->assertNotEquals($questionPicture->image()->onlyTrashed()->first()->deleted_at, null);

        $this->put(route('question.restore.picture', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id, 'picture_id' => $questionPicture->id]))
            ->assertRedirect(route('question.edit', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id]));

        $this->assertDatabaseHas((new Upload)->getTable(), ['deleted_at' => null, 'uploadable_id' => $questionPicture->id, 'uploadable_type' => 'App\QuestionPicture']);
    }
}
