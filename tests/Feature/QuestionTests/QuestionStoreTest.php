<?php

namespace Tests\Feature\QuestionTests;

use App\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Question;

class QuestionStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_store_a_question()
    {
        $this->signIn();

        $map = $this->createMap();

        $data = factory(Question::class)->raw(['map_id' => null]);

        $this->post(route('question.store', $map->id), $data)->assertForbidden();
    }

    private $question_max_template_size = 15000;

    /** @test */
    public function a_superadmin_can_store_a_question()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw(['map_id' => null]);

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->question_max_template_size);

        $this->post(route('question.store', $map->id), $data)
            ->assertRedirect(route('question.edit', ['map' => $map->id, 'question' => Question::first()->id]));

        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout']]);
        $this->assertCount(1, $map->fresh()->questions);
    }

    /** @test */
    public function a_editor_can_store_a_question()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw();

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->question_max_template_size);

        $this->post(route('question.store', $map->id), $data)
            ->assertRedirect(route('question.edit', ['map' => $map->id, 'question' => Question::first()->id]));

        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout']]);
        $this->assertCount(1, $map->fresh()->questions);
    }

    /** @test */
    public function cannot_store_a_question_when_no_map_template_is_available()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap([], 1, false);

        $data = factory(Question::class)->raw();

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->question_max_template_size);

        $this->post(route('question.store', $map->id), $data)->assertStatus(403);

        $this->assertDatabaseMissing((new Question)->getTable(), ['callout' => $data['callout']]);
        $this->assertCount(0, $map->fresh()->questions);
    }

    /** @test */
    public function is_always_unpublushed_while_storing_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw();

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->question_max_template_size);

        $this->post(route('question.store', $map->id), $data)->isSuccessful();

        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout'], 'published' => false]);
    }

    /** @test */
    public function callout_is_required_while_storing_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw(['callout' => '']);

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->question_max_template_size);

        $this->post(route('question.store', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('callout')[0],"The callout field is required.");

        $this->assertDatabaseMissing((new Question)->getTable(), ['callout' => $data['callout']]);
    }

    /*
     * Lets say you have a map called Netherlands
     * And you have a callout called Amsterdam
     * Then you cannot add a callout called Amsterdam under the map Netherlands again
     * Since it has the same parent
     */
    /** @test */
    public function callout_is_unique_in_the_parent_relation_of_map_while_storing_a_question()
    {
        $this->signIn(['editor' => true]);

        $callout = 'Not unique callout';

        $map = $this->createMap();

        $this->createQuestion(['callout' => $callout, 'map_id' => $map->id]);

        $data = factory(Question::class)->raw(['callout' => $callout]);

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->question_max_template_size);

        $this->post(route('question.store', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('callout')[0],"The callout has already been taken.");

        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout']]);
        $this->assertDatabaseCount((new Question())->getTable(), 1); // Count is one since there already exists one record
    }

    /*
     * Lets say you have a map called Netherlands and Holland
     * And you want a callout called Amsterdam for both maps
     * Since the parents are not the same you can.
     */
    /** @test */
    public function the_same_callout_can_be_created_for_different_maps_while_storing_a_question()
    {
        $this->signIn(['editor' => true]);

        $callout = 'Not unique callout';

        $mapWithQuestion = $this->createMap();
        $this->createQuestion(['callout' => $callout, 'map_id' => $mapWithQuestion->id]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw(['callout' => $callout]);

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->question_max_template_size);

        $this->post(route('question.store', $map->id), $data)->isSuccessful();

        $this->assertDatabaseCount((new Question)->getTable(), 2);
    }

    /** @test */
    public function a_template_can_be_uploaded_while_storing_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw();

        $data['template'] = UploadedFile::fake()->image('template.jpg')->size($this->question_max_template_size);

        $this->post(route('question.store', $map->id), $data)->isSuccessful();

        $this->assertDatabaseHas((new Upload)->getTable(), ['uploadable_id' => $map->fresh()->questions->first()->id, 'uploadable_type' => 'App\Question', 'name' => 'template.jpg']);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout']]);
    }

    /** @test */
    public function a_template_is_required_while_storing_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw();

        $this->post(route('question.store', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('template')[0],"The template field is required.");

        $this->assertDatabaseMissing((new Upload)->getTable(), ['uploadable_type' => 'App\Question']);
        $this->assertDatabaseMissing((new Question)->getTable(), ['callout' => $data['callout']]);
    }

    /** @test */
    public function a_template_must_be_a_type_of_image_while_storing_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw();

        $data['template'] = 'Some text and not a image';

        $this->post(route('question.store', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('template')[0],"The template must be an image.");

        $this->assertDatabaseMissing((new Upload)->getTable(), ['uploadable_type' => 'App\Question']);
        $this->assertDatabaseMissing((new Question)->getTable(), ['callout' => $data['callout']]);
    }

    /** @test */
    public function template_size_cannot_be_greater_then_x_while_storing_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $data = factory(Question::class)->raw();

        $data['template'] = UploadedFile::fake()->image('template.jpg')->size(($this->question_max_template_size + 1));

        $this->post(route('question.store', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('template')[0],"The template may not be greater than 15000 kilobytes.");

        $this->assertDatabaseMissing((new Upload)->getTable(), ['uploadable_type' => 'App\Question']);
        $this->assertDatabaseMissing((new Question)->getTable(), ['callout' => $data['callout']]);
    }
}
