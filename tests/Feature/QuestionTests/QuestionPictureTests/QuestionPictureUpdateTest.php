<?php

namespace Tests\Feature\QuestionTests\QuestionPictureTests;

use App\Map;
use App\Question;
use App\QuestionPicture;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionPictureUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_update_a_question_picture()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $picture = $this->createQuestionPicture();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $this->patch(route('question.update.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]), $data)->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_update_a_question_picture()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $picture = $this->createQuestionPicture();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $this->patch(route('question.update.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $picture->question->map->id, 'question' => $picture->question->id]));

        $this->assertDatabaseHas((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function a_editor_can_update_a_question_picture()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $picture = $this->createQuestionPicture();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $this->patch(route('question.update.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $picture->question->map->id, 'question' => $picture->question->id]));

        $this->assertDatabaseHas((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function cannot_update_a_question_picture_when_the_map_is_not_the_parent_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $questionPicture = $this->createQuestionPicture(); // Parent map is ID 1 and the question is linked to it with ID 1 and the question picture is linked to that.

        $fakeMap = $this->createMap(); // fakeMap is ID 2

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());
        $this->assertCount(1, QuestionPicture::all());

        $this->expectException(ModelNotFoundException::class);

        $this->patch(route('question.update.picture', ['map' => $fakeMap->id, 'question' => $questionPicture->question->id, 'picture', $questionPicture->id]), $data);
    }

    /** @test */
    public function cannot_update_a_question_picture_when_the_question_picture_is_not_a_child_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $questionPicture = $this->createQuestionPicture(); // Parent map is ID 1 and the question is linked to it with ID 1 and the question picture is linked to that.

        $fakeQuestionPicture = $this->createQuestionPicture(); // $fakeQuestionPicture is ID 2

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $this->assertCount(2, Map::all());
        $this->assertCount(2, Question::all());
        $this->assertCount(2, QuestionPicture::all());

        $this->expectException(ModelNotFoundException::class);

        $this->patch(route('question.update.picture', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id, $fakeQuestionPicture->id]), $data);
    }

    /** @test */
    public function difficulty_is_required_while_updating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $picture = $this->createQuestionPicture();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'difficulty' => '']);

        $this->patch(route('question.update.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.difficulty')[0], 'The difficulty field is required.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function difficulty_must_be_a_type_of_numeric_while_updating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $picture = $this->createQuestionPicture();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'difficulty' => 'This is a string']);

        $this->patch(route('question.update.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.difficulty')[0], 'The difficulty must be a number.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function difficulty_must_be_a_numeric_value_between_1_and_5_while_updating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $picture = $this->createQuestionPicture();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'difficulty' => 0]);

        $this->patch(route('question.update.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.difficulty')[0], 'The difficulty must be between 1 and 5.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function active_is_required_while_updating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $picture = $this->createQuestionPicture();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'active' => null]);

        $this->patch(route('question.update.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.active')[0], 'The active field is required.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['active' => $data['picture']['active']]);
    }

    /** @test */
    public function active_must_be_a_type_of_boolean_while_updating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $picture = $this->createQuestionPicture();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'active' => 'Some string']);

        $this->patch(route('question.update.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.active')[0], 'The active field must be true or false.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['active' => $data['picture']['active']]);
    }
}
