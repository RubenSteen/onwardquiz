<?php

namespace Tests\Feature\QuestionFakeAnswerTests;

use App\Map;
use App\Question;
use App\QuestionFakeAnswer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionFakeAnswerDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_delete_a_question_fake_answer()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $fakeAnswer = $this->createQuestionFakeAnswer();

        $this->delete(route('question.destroy.fake-answer', ['map' => $fakeAnswer->question->map->id, 'question' => $fakeAnswer->question->id, 'fakeAnswer' => $fakeAnswer->id]))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_delete_a_question_fake_answer()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $fakeAnswer = $this->createQuestionFakeAnswer();

        $this->assertDatabaseHas((new QuestionFakeAnswer)->getTable(), ['callout' => $fakeAnswer->callout]);

        $this->delete(route('question.destroy.fake-answer', ['map' => $fakeAnswer->question->map->id, 'question' => $fakeAnswer->question->id, 'fakeAnswer' => $fakeAnswer->id]))
            ->assertRedirect(route('question.edit', ['map' => $fakeAnswer->question->map->id, 'question' => $fakeAnswer->question->id]));

        $this->assertDatabaseMissing((new QuestionFakeAnswer)->getTable(), ['callout' => $fakeAnswer->callout]);
    }

    /** @test */
    public function a_editor_can_delete_a_question_fake_answer()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $fakeAnswer = $this->createQuestionFakeAnswer();

        $this->assertDatabaseHas((new QuestionFakeAnswer)->getTable(), ['callout' => $fakeAnswer->callout]);

        $this->delete(route('question.destroy.fake-answer', ['map' => $fakeAnswer->question->map->id, 'question' => $fakeAnswer->question->id, 'fakeAnswer' => $fakeAnswer->id]))
            ->assertRedirect(route('question.edit', ['map' => $fakeAnswer->question->map->id, 'question' => $fakeAnswer->question->id]));

        $this->assertDatabaseMissing((new QuestionFakeAnswer)->getTable(), ['callout' => $fakeAnswer->callout]);
    }

    /** @test */
    public function cannot_delete_a_question_fake_answer_when_the_map_is_not_the_parent_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $questionFakeAnswer = $this->createQuestionFakeAnswer(); // Parent map is ID 1 and the question is linked to it with ID 1 and the question fake answer is linked to that with ID 1.

        $fakeMap = $this->createMap(); // fakeMap is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());
        $this->assertCount(1, QuestionFakeAnswer::all());

        $this->expectException(ModelNotFoundException::class);

        $this->delete(route('question.destroy.picture', ['map' => $fakeMap->id, 'question' => $questionFakeAnswer->question->id, 'fakeAnswer', $questionFakeAnswer->id]));
    }

    /** @test */
    public function cannot_delete_a_question_fake_answer_when_the_question_picture_is_not_a_child_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $questionFakeAnswer = $this->createQuestionFakeAnswer(); // Parent map is ID 1 and the question is linked to it with ID 1 and the question fake answer is linked to that with ID 1.

        $fakeQuestionFakeAnswer = $this->createQuestionFakeAnswer(); // $fakeQuestionFakeAnswer is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(2, Question::all());
        $this->assertCount(2, QuestionFakeAnswer::all());

        $this->expectException(ModelNotFoundException::class);

        $this->delete(route('question.destroy.picture', ['map' => $questionFakeAnswer->question->map->id, 'question' => $questionFakeAnswer->question->id, 'fakeAnswer', $fakeQuestionFakeAnswer->id]));
    }
}
