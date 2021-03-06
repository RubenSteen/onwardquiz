<?php

namespace Tests\Feature\QuestionTests;

use App\Map;
use App\Question;
use App\QuestionFakeAnswer;
use App\QuestionPicture;
use App\Upload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_delete_a_question()
    {
        $question = $this->createQuestion();

        $this->assertCount(1, Question::all());

        $this->signIn();

        $this->delete(route('question.destroy', ['map' => $question->map->id, 'question' => $question->id]))->assertForbidden();

        $this->assertCount(1, Question::all());
    }

    /** @test */
    public function a_superadmin_can_delete_a_question()
    {
        $question = $this->createQuestion();

        $this->assertCount(1, Question::all());

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->delete(route('question.destroy', ['map' => $question->map->id, 'question' => $question->id]))
            ->assertRedirect(route('map.edit', $question->map->id));

        $this->assertCount(1, Question::withTrashed()->get());
        $this->assertNotEquals($question->fresh()->deleted_at, null);
    }

    /** @test */
    public function a_editor_can_delete_a_question()
    {
        $question = $this->createQuestion();

        $this->assertCount(1, Question::all());

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->delete(route('question.destroy', ['map' => $question->map->id, 'question' => $question->id]))
            ->assertRedirect(route('map.edit', $question->map->id));

        $this->assertCount(1, Question::withTrashed()->get());
        $this->assertNotEquals($question->fresh()->deleted_at, null);
    }

    /** @test */
    public function cannot_delete_a_question_when_the_map_is_not_the_given_map()
    {
        $this->withoutExceptionHandling();

        $question = $this->createQuestion(); // Parent map is ID 1 and the question is linked to it.

        $fakeMap = $this->createMap(); // fakeMap is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->expectException(ModelNotFoundException::class);

        $this->delete(route('question.destroy', ['map' => $fakeMap->id, 'question' => $question->id]));
    }

    /** @test */
    public function pictures_get_deleted_when_a_question_is_deleted()
    {
        $question = $this->createQuestion();

        $this->createQuestionPicture(['question_id' => $question->id], 10);

        $this->assertCount(1, Question::all());
        $this->assertCount(10, QuestionPicture::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('question.destroy', ['map' => $question->map->id, 'question' => $question->id]))->isSuccessful();

        $this->assertCount(0, Question::all());
        $this->assertCount(0, QuestionPicture::all());
    }

    /** @test */
    public function fake_answers_get_deleted_when_a_question_is_deleted()
    {
        $question = $this->createQuestion();

        $this->createQuestionFakeAnswer(['question_id' => $question->id], 10);

        $this->assertCount(1, Question::all());
        $this->assertCount(10, QuestionFakeAnswer::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('question.destroy', ['map' => $question->map->id, 'question' => $question->id]))->isSuccessful();

        $this->assertCount(0, Question::all());
        $this->assertCount(0, QuestionFakeAnswer::all());
    }

    /** @test */
    public function does_not_throw_a_server_error_when_no_pictures_exist_when_a_question_is_deleted()
    {
        $question = $this->createQuestion();

        $this->assertCount(1, Question::all());
        $this->assertCount(0, QuestionPicture::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('question.destroy', ['map' => $question->map->id, 'question' => $question->id]))->isSuccessful();

        $this->assertCount(0, Question::all());
        $this->assertCount(0, QuestionPicture::all());
    }

    /** @test */
    public function template_gets_deleted_when_a_question_is_deleted()
    {
        $question = $this->createQuestion();

        $this->assertCount(1, Question::all());
        $this->assertCount(1, Upload::all());

        $this->signIn(['editor' => true]);

        $this->delete(route('question.destroy', ['map' => $question->map->id, 'question' => $question->id]))->isSuccessful();

        $this->assertCount(0, Question::all());
        $this->assertCount(1, Upload::withTrashed()->get());
        $this->assertNotEquals($question->template()->withTrashed()->first()->deleted_at, null);
    }

    /** @test */
    public function unpublish_the_map_when_less_than_4_questions_are_published_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap(['published' => 1]);

        $question = $this->createQuestion(['map_id' => $map->id, 'published' => 1], 4)
            ->first();

        $this->delete(route('question.destroy', ['map' => $map->id, 'question' => $question->id]))->isSuccessful();

        $this->assertEquals(false, $map->fresh()->published);
    }

    /** @test */
    public function do_not_unpublish_the_map_when_more_than_4_questions_are_still_published_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap(['published' => 1]);

        $question = $this->createQuestion(['map_id' => $map->id, 'published' => 1], 5)
            ->first();

        $this->delete(route('question.destroy', ['map' => $map->id, 'question' => $question->id]))->isSuccessful();

        $this->assertEquals(true, $map->fresh()->published);
    }
}
