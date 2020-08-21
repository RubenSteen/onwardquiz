<?php

namespace Tests\Feature\QuestionFakeAnswerTests;

use App\Map;
use App\Question;
use App\QuestionFakeAnswer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionFakeAnswerStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_create_a_question_fake_answer()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $question = $this->createQuestion();

        $data = factory(QuestionFakeAnswer::class)->raw();

        $this->post(route('question.store.fake-answer', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_create_a_question_fake_answer()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $question = $this->createQuestion();

        $data['fake-answer'] = factory(QuestionFakeAnswer::class)->raw(['question_id' => null]);

        $this->post(route('question.store.fake-answer', ['map' => $question->map->id, 'question' => $question->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $question->map->id, 'question' => $question->id]));

        $this->assertDatabaseHas((new QuestionFakeAnswer)->getTable(), ['callout' => $data['fake-answer']['callout']]);
    }

    /** @test */
    public function a_editor_can_create_a_question_fake_answer()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $question = $this->createQuestion();

        $data['fake-answer'] = factory(QuestionFakeAnswer::class)->raw(['question_id' => null]);

        $this->post(route('question.store.fake-answer', ['map' => $question->map->id, 'question' => $question->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $question->map->id, 'question' => $question->id]));

        $this->assertDatabaseHas((new QuestionFakeAnswer)->getTable(), ['callout' => $data['fake-answer']['callout']]);
    }

    /** @test */
    public function cannot_create_a_question_fake_answer_when_the_map_is_not_the_parent_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $question = $this->createQuestion(); // Parent map is ID 1 and the question is linked to it with ID 1.

        $fakeMap = $this->createMap(); // fakeQuestion is ID 2

        $data['picture'] = factory(QuestionFakeAnswer::class)->raw(['question_id' => null]);

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());

        $this->expectException(ModelNotFoundException::class);

        $this->post(route('question.store.fake-answer', ['map' => $fakeMap->id, 'question' => $question->id]), $data);
    }

    /** @test */
    public function callout_is_required_while_creating_a_question_fake_answer()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion();

        $data['fake-answer'] = factory(QuestionFakeAnswer::class)->raw(['question_id' => null, 'callout' => '']);

        $this->post(route('question.store.fake-answer', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('fake-answer.callout')[0], 'The callout field is required.');

        $this->assertDatabaseMissing((new QuestionFakeAnswer)->getTable(), ['callout' => $data['fake-answer']['callout']]);
    }

    /** @test */
    public function callout_must_be_unique_to_the_given_question_while_creating_a_question_fake_answer()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion();

        $data['fake-answer'] = factory(QuestionFakeAnswer::class)->raw(['question_id' => null, 'callout' => 'Some Callout']);

        $this->post(route('question.store.fake-answer', ['map' => $question->map->id, 'question' => $question->id]), $data)->isSuccessful();
        $this->post(route('question.store.fake-answer', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('fake-answer.callout')[0], 'The callout has already been taken.');
    }

    /** @test */
    public function can_create_2_different_named_callouts_to_the_given_question_while_creating_a_question_fake_answer()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion();

        $data['first']['fake-answer'] = factory(QuestionFakeAnswer::class)->raw(['question_id' => null, 'callout' => 'Some callout']);
        $data['second']['fake-answer'] = factory(QuestionFakeAnswer::class)->raw(['question_id' => null, 'callout' => 'Some other callout']);

        $this->post(route('question.store.fake-answer', ['map' => $question->map->id, 'question' => $question->id]), $data['first'])->isSuccessful();
        $this->post(route('question.store.fake-answer', ['map' => $question->map->id, 'question' => $question->id]), $data['second'])->isSuccessful();

        $this->assertDatabaseHas((new QuestionFakeAnswer)->getTable(), ['callout' => $data['first']['fake-answer']['callout']]);
        $this->assertDatabaseHas((new QuestionFakeAnswer)->getTable(), ['callout' => $data['second']['fake-answer']['callout']]);
    }
}
