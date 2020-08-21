<?php

namespace Tests\Feature\SimilarQuestionTests;

use App\Map;
use App\Question;
use App\QuestionPicture;
use App\SimilarQuestion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimilarQuestionDetachTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_detach_similar_questions()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $firstQuestion = $this->createQuestion();

        $data = factory(QuestionPicture::class)->raw();

        $this->delete(route('question.detach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_detach_similar_questions()
    {
        $this->withoutExceptionHandling();
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);

        $firstQuestion->similar_questions()->attach($secondQuestion->id);
        $firstQuestion->question_is_similar_to()->attach($secondQuestion->id);

        $data['similar_question']['id'] = $secondQuestion->id;

        $this->delete(route('question.detach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]));

        // Will look if the records are detached both ways.
        $this->assertDatabaseMissing((new SimilarQuestion)->getTable(), [
            'similar_question_id' => $data['similar_question']['id'],
            'question_id' => $firstQuestion->id
        ]);

        $this->assertDatabaseMissing((new SimilarQuestion)->getTable(), [
            'question_id' => $data['similar_question']['id'],
            'similar_question_id' => $firstQuestion->id,
        ]);
    }

    /** @test */
    public function a_editor_can_detach_similar_questions()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);

        $firstQuestion->similar_questions()->attach($secondQuestion->id);
        $firstQuestion->question_is_similar_to()->attach($secondQuestion->id);

        $data['similar_question']['id'] = $secondQuestion->id;

        $this->delete(route('question.detach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]));

        // Will look if the records are detached both ways.
        $this->assertDatabaseMissing((new SimilarQuestion)->getTable(), [
            'similar_question_id' => $data['similar_question']['id'],
            'question_id' => $firstQuestion->id
        ]);

        $this->assertDatabaseMissing((new SimilarQuestion)->getTable(), [
            'question_id' => $data['similar_question']['id'],
            'similar_question_id' => $firstQuestion->id,
        ]);
    }

    /** @test */
    public function only_the_specified_similar_question_will_get_detached_and_the_others_will_not()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);
        $thirdQuestion = $this->createQuestion(['map_id' => $firstQuestion->map->id]);

        $firstQuestion->similar_questions()->attach($secondQuestion->id);
        $firstQuestion->question_is_similar_to()->attach($secondQuestion->id);
        $firstQuestion->similar_questions()->attach($thirdQuestion->id);
        $firstQuestion->question_is_similar_to()->attach($thirdQuestion->id);

        $data['similar_question']['id'] = $secondQuestion->id;

        $this->delete(route('question.detach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data);

        $this->assertDatabaseCount((new SimilarQuestion)->getTable(), 2);

        $this->assertDatabaseMissing((new SimilarQuestion)->getTable(), [
            'question_id' => $data['similar_question']['id'],
            'similar_question_id' => $secondQuestion->id,
        ]);
    }

    /** @test */
    public function cannot_detach_a_similar_question_when_the_map_is_not_the_parent_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $question = $this->createQuestion(); // Parent map is ID 1 and the question picture is linked to it.

        $fakeMap = $this->createMap(); // fakeQuestion is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());

        $this->expectException(ModelNotFoundException::class);

        $this->delete(route('question.detach.similar-question', ['map' => $fakeMap->id, 'question' => $question->id]));
    }

    /** @test */
    public function cannot_detach_a_similar_question_when_the_request_data_holds_a_question_that_is_attached_to_the_given_map()
    {
        $this->signIn(['editor' => true]);

        $firstQuestion = $this->createQuestion();
        $secondQuestion = $this->createQuestion();

        $data['similar_question']['id'] = $secondQuestion->id;

        $this->delete(route('question.detach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('similar_question.id')[0], 'The selected similar question is registered as similar to the given question.');
    }

    /** @test */
    public function similar_question_id_must_be_required_when_detaching_a_similar_question()
    {
        $this->signIn(['editor' => true]);

        $firstQuestion = $this->createQuestion();

        $data = [];

        $this->delete(route('question.detach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('similar_question.id')[0], 'The similar question id field is required.');
    }

    /** @test */
    public function similar_question_id_must_be_an_integer_when_detaching_a_similar_question()
    {
        $this->signIn(['editor' => true]);

        $firstQuestion = $this->createQuestion();

        $data['similar_question']['id'] = 'a string';

        $this->delete(route('question.detach.similar-question', ['map' => $firstQuestion->map->id, 'question' => $firstQuestion->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('similar_question.id')[0], 'The similar question id must be an integer.');
    }
}
