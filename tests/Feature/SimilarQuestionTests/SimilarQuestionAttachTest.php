<?php

namespace Tests\Feature\SimilarQuestionTests;

use App\Map;
use App\Question;
use App\QuestionPicture;
use App\SimilarQuestion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimilarQuestionAttachTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_attach_similar_questions()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $firstQuestion = $this->createQuestion();

        $data = factory(QuestionPicture::class)->raw();

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_attach_similar_questions()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);

        $data['similar_question']['id'] = $secondQuestion->id;

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]));

        // Will look if the records are attached both ways.
        $this->assertDatabaseHas((new SimilarQuestion)->getTable(), [
            'similar_question_id' => $data['similar_question']['id'],
            'question_id' => $firstQuestion->id,
        ]);

        $this->assertDatabaseHas((new SimilarQuestion)->getTable(), [
            'question_id' => $data['similar_question']['id'],
            'similar_question_id' => $firstQuestion->id,
        ]);
    }

    /** @test */
    public function a_editor_can_attach_similar_questions()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);

        $data['similar_question']['id'] = $secondQuestion->id;

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]));

        // Will look if the records are attached both ways.
        $this->assertDatabaseHas((new SimilarQuestion)->getTable(), [
            'similar_question_id' => $data['similar_question']['id'],
            'question_id' => $firstQuestion->id,
        ]);

        $this->assertDatabaseHas((new SimilarQuestion)->getTable(), [
            'question_id' => $data['similar_question']['id'],
            'similar_question_id' => $firstQuestion->id,
        ]);
    }

    /** @test */
    public function a_question_can_have_multiple_similar_questions()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);
        $thirdQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);

        $data['second']['similar_question']['id'] = $secondQuestion->id;
        $data['third']['similar_question']['id'] = $thirdQuestion->id;

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data['second'])->isSuccessful();
        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data['third'])->isSuccessful();

        $this->assertDatabaseCount((new SimilarQuestion)->getTable(), 4);
    }

    /** @test */
    public function a_question_cannot_have_multiple_similar_questions_of_the_same_id()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);

        $data['similar_question']['id'] = $secondQuestion->id;

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->isSuccessful();

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('similar_question.id')[0], 'The selected question is already registered as similar question.');

        $this->assertDatabaseCount((new SimilarQuestion)->getTable(), 2);
    }

    /** @test */
    public function cannot_attach_a_similar_question_when_the_map_is_not_the_parent_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $question = $this->createQuestion(); // Parent map is ID 1 and the question picture is linked to it.

        $fakeMap = $this->createMap(); // fakeQuestion is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());

        $this->expectException(ModelNotFoundException::class);

        $this->post(route('question.attach.similar-question', ['map' => $fakeMap->id, 'question' => $question->id]));
    }

    /** @test */
    public function cannot_attach_a_similar_question_when_the_request_data_holds_a_question_that_is_not_a_child_of_the_given_map()
    {
        $this->signIn(['editor' => true]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion();

        $data['similar_question']['id'] = $secondQuestion->id;

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('similar_question.id')[0], 'The selected question is not part of the given map.');
    }

    /** @test */
    public function similar_question_id_must_be_required_when_attaching_a_similar_question()
    {
        $this->signIn(['editor' => true]);

        $firstQuestion = $this->createQuestion();

        $data = [];

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('similar_question.id')[0], 'The similar question id field is required.');
    }

    /** @test */
    public function similar_question_id_must_be_an_integer_when_attaching_a_similar_question()
    {
        $this->signIn(['editor' => true]);

        $firstQuestion = $this->createQuestion();

        $data['similar_question']['id'] = 'a string';

        $this->post(route('question.attach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('similar_question.id')[0], 'The similar question id must be an integer.');
    }
}
