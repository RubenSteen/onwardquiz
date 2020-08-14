<?php

namespace Tests\Feature\QuestionTests\QuestionPictureTests;

use App\Map;
use App\Question;
use App\QuestionPicture;
use App\Upload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionPictureDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_delete_a_question_picture()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $picture = $this->createQuestionPicture();

        $this->delete(route('question.delete.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_delete_a_question_picture()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $picture = $this->createQuestionPicture();

        $this->assertCount(1, QuestionPicture::all());

        $this->delete(route('question.delete.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]))
            ->assertRedirect(route('question.edit', ['map' => $picture->question->map->id, 'question' => $picture->question->id]));

        $this->assertCount(1, QuestionPicture::withTrashed()->get());
        $this->assertNotEquals($picture->fresh()->deleted_at, null);
    }

    /** @test */
    public function a_editor_can_delete_a_question_picture()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $picture = $this->createQuestionPicture();

        $this->assertCount(1, QuestionPicture::all());

        $this->delete(route('question.delete.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]))
            ->assertRedirect(route('question.edit', ['map' => $picture->question->map->id, 'question' => $picture->question->id]));

        $this->assertCount(1, QuestionPicture::withTrashed()->get());
        $this->assertNotEquals($picture->fresh()->deleted_at, null);
    }

    /** @test */
    public function cannot_delete_a_question_picture_when_the_map_is_not_the_parent_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $questionPicture = $this->createQuestionPicture(); // Parent map is ID 1 and the question is linked to it with ID 1 and the question picture is linked to that.

        $fakeMap = $this->createMap(); // fakeMap is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());
        $this->assertCount(1, QuestionPicture::all());

        $this->expectException(ModelNotFoundException::class);

        $this->delete(route('question.delete.picture', ['map' => $fakeMap->id, 'question' => $questionPicture->question->id, 'picture', $questionPicture->id]));
    }

    /** @test */
    public function cannot_delete_a_question_picture_when_the_question_picture_is_not_a_child_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $questionPicture = $this->createQuestionPicture(); // Parent map is ID 1 and the question is linked to it with ID 1 and the question picture is linked to that.

        $fakeQuestionPicture = $this->createQuestionPicture(); // $fakeQuestionPicture is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(2, Question::all());
        $this->assertCount(2, QuestionPicture::all());

        $this->expectException(ModelNotFoundException::class);

        $this->delete(route('question.delete.picture', ['map' => $questionPicture->question->map->id, 'question' => $questionPicture->question->id, 'picture', $fakeQuestionPicture->id]));
    }

    /** @test */
    public function image_gets_deleted_when_a_question_is_deleted()
    {
        $this->signIn(['editor' => true]);

        $picture = $this->createQuestionPicture();

        $this->assertDatabaseHas((new Upload)->getTable(), ['uploadable_id' => $picture->id, 'uploadable_type' => 'App\QuestionPicture']);

        $this->delete(route('question.delete.picture', ['map' => $picture->question->map->id, 'question' => $picture->question->id, 'picture' => $picture->id]));

        $this->assertNotEquals($picture->image()->onlyTrashed()->first()->deleted_at, null);
    }
}
