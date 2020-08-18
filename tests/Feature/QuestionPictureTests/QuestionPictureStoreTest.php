<?php

namespace Tests\Feature\QuestionPictureTests;

use App\Map;
use App\Question;
use App\QuestionPicture;
use App\Upload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class QuestionPictureStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_create_a_question_picture()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $question = $this->createQuestion();

        $data = factory(QuestionPicture::class)->raw();

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertForbidden();
    }

    private $image_max_template_size = 15000;

    /** @test */
    public function a_superadmin_can_create_a_question_picture()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $question = $this->createQuestion();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $question->map->id, 'question' => $question->id]));

        $this->assertDatabaseHas((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function a_editor_can_create_a_question_picture()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $question = $this->createQuestion();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $question->map->id, 'question' => $question->id]));

        $this->assertDatabaseHas((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function cannot_create_a_question_picture_when_the_map_is_not_the_parent_of_the_given_question()
    {
        $this->withoutExceptionHandling();

        $this->signIn(['editor' => true]);

        $question = $this->createQuestion(); // Parent map is ID 1 and the question picture is linked to it.

        $fakeMap = $this->createMap(); // fakeQuestion is ID 2

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());

        $this->expectException(ModelNotFoundException::class);

        $this->post(route('question.store.picture', ['map' => $fakeMap->id, 'question' => $question->id]), $data);
    }

    /** @test */
    public function difficulty_is_required_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'difficulty' => '']);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.difficulty')[0], 'The difficulty field is required.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function difficulty_must_be_a_type_of_numeric_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'difficulty' => 'This is a string']);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.difficulty')[0], 'The difficulty must be a number.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function difficulty_must_be_a_numeric_value_between_1_and_5_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'difficulty' => 0]);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.difficulty')[0], 'The difficulty must be between 1 and 5.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['difficulty' => $data['picture']['difficulty']]);
    }

    /** @test */
    public function active_is_required_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'active' => null]);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.active')[0], 'The active field is required.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['active' => $data['picture']['active']]);
    }

    /** @test */
    public function active_must_be_a_type_of_boolean_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion();

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null, 'active' => 'Some string']);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.active')[0], 'The active field must be true or false.');

        $this->assertDatabaseMissing((new QuestionPicture)->getTable(), ['active' => $data['picture']['active']]);
    }

    /** @test */
    public function a_image_can_be_uploaded_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion([], 1, false);

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size($this->image_max_template_size);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->isSuccessful();

        $this->assertDatabaseHas((new Upload)->getTable(), ['name' => $data['picture']['image']->getClientOriginalName()]);
    }

    /** @test */
    public function image_is_required_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion([], 1, false);

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.image')[0], 'The image field is required.');

        $this->assertDatabaseCount((new Upload)->getTable(), 0);
    }

    /** @test */
    public function image_must_be_a_type_of_image_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion([], 1, false);

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $data['picture']['image'] = 'Some string';

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.image')[0], 'The uploaded file must be an image.');

        $this->assertDatabaseCount((new Upload)->getTable(), 0);
    }

    /** @test */
    public function image_size_can_be_max_x_while_creating_a_question_picture()
    {
        $this->signIn(['editor' => true]);

        $question = $this->createQuestion([], 1, false);

        $data['picture'] = factory(QuestionPicture::class)->raw(['question_id' => null]);

        $data['picture']['image'] = UploadedFile::fake()->image('image.jpg')->size(($this->image_max_template_size + 1));

        $this->post(route('question.store.picture', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('picture.image')[0], 'The image may not be greater than 15000 kilobytes.');

        $this->assertDatabaseCount((new Upload)->getTable(), 0);
    }
}
