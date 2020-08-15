<?php

namespace Tests\Feature\QuestionTests;

use App\Map;
use App\Question;
use App\Upload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class QuestionUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_update_a_question()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $question = $this->createQuestion();

        $data = factory(Question::class)->raw(['map_id' => null, 'callout' => 'Updated name']);

        $this->patch(route('question.update', ['map' => $question->map->id, 'question' => $question->id]), $data)->assertForbidden();
    }

    private $question_max_template_size = 15000;

    /** @test */
    public function a_superadmin_can_update_a_question()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $data = factory(Question::class)->raw(['map_id' => null, 'callout' => 'Updated name']);

        $this->patch(route('question.update', ['map' => $question->map->id, 'question' => $question->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $question->map->id, 'question' => $question->id]));

        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => 'Updated name']);
    }

    /** @test */
    public function a_editor_can_update_a_question()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $data = factory(Question::class)->raw(['map_id' => null, 'callout' => 'Updated name']);

        $this->patch(route('question.update', ['map' => $question->map->id, 'question' => $question->id]), $data)
            ->assertRedirect(route('question.edit', ['map' => $question->map->id, 'question' => $question->id]));

        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => 'Updated name']);
    }

    /** @test */
    public function cannot_update_a_question_when_no_map_template_is_available()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap([], 1, false);

        $question = $this->createQuestion(['map_id' => $map->id]);

        $data = factory(Question::class)->raw(['map_id' => null, 'callout' => 'Updated name']);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->assertStatus(403);

        $this->assertDatabaseMissing((new Question)->getTable(), ['callout' => 'Updated name']);
    }

    /** @test */
    public function cannot_update_a_question_when_the_map_is_not_the_given_map()
    {
        $this->withoutExceptionHandling();

        $question = $this->createQuestion(); // Parent map is ID 1 and the question is linked to it.

        $fakeMap = $this->createMap(); // fakeMap is ID 2

        $this->assertCount(2, Map::all());
        $this->assertCount(1, Question::all());

        $this->signIn(['editor' => true]);

        $this->expectException(ModelNotFoundException::class);

        $this->get(route('question.edit', ['map' => $fakeMap->id, 'question' => $question->id]));
    }

    /** @test */
    public function published_is_required_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $data = factory(Question::class)->raw(['map_id' => null, 'published' => null]);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('published')[0], 'The published field is required.');

        $this->assertDatabaseMissing((new Question)->getTable(), ['callout' => $data['callout']]);
    }

    /** @test */
    public function callout_is_required_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $data = factory(Question::class)->raw(['map_id' => null, 'callout' => '']);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('callout')[0], 'The callout field is required.');

        $this->assertDatabaseMissing((new Question)->getTable(), ['callout' => $data['callout']]);
    }

    /*
     * Lets say you have a map called Netherlands
     * And you have a callout called Amsterdam
     * Then you cannot update a callout to Amsterdam under the same map Netherlands
     */

    /** @test */
    public function callout_is_unique_in_the_parent_relation_of_map_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $callout = 'Not unique callout';

        $map = $this->createMap();

        $this->createQuestion(['callout' => $callout, 'map_id' => $map->id]);

        $question = $this->createQuestion(['map_id' => $map->id]);

        $data = factory(Question::class)->raw(['map_id' => null, 'callout' => $callout]);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('callout')[0], 'The callout has already been taken.');

        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout']]);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $question->callout]); // Did not update
        $this->assertDatabaseCount((new Question())->getTable(), 2); // Count is one since there already exists one record
    }

    /*
     * Lets say you have a map called Netherlands and Holland
     * And you want a callout called Amsterdam for both maps
     * Since the parents are not the same you can.
     */

    /** @test */
    public function the_same_callout_can_be_created_for_different_maps_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $callout = 'Amsterdam';

        $mapNetherlands = $this->createMap(['name' => 'Netherlands']);
        $questionNetherlands = $this->createQuestion(['callout' => $callout, 'map_id' => $mapNetherlands->id]);

        $mapHolland = $this->createMap(['name' => 'Holland']);
        $questionHolland = $this->createQuestion(['map_id' => $mapHolland->id]);

        $this->assertDatabaseHas((new Question)->getTable(), ['id' => $questionNetherlands->id, 'callout' => $callout]);

        $data = factory(Question::class)->raw(['callout' => $callout]);

        $this->patch(route('question.update', ['map' => $mapHolland->id, 'question' => $questionHolland->id]), $data)->isSuccessful();

        $this->assertDatabaseCount((new Question)->getTable(), 2);
        $this->assertDatabaseHas((new Question)->getTable(), ['id' => $questionNetherlands->id, 'callout' => $callout]);
        $this->assertDatabaseHas((new Question)->getTable(), ['id' => $questionHolland->id, 'callout' => $callout]);
    }

    /** @test */
    public function a_template_can_be_uploaded_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $data = factory(Question::class)->raw(['map_id' => null]);

        $data['template'] = UploadedFile::fake()->image('template.jpg')->size($this->question_max_template_size);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->isSuccessful();

        $this->assertDatabaseHas((new Upload)->getTable(), ['uploadable_id' => $question->id, 'uploadable_type' => 'App\Question', 'name' => 'template.jpg']);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout']]);
    }

    /** @test */
    public function a_template_is_nullable_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id], 1, false);

        $data = factory(Question::class)->raw(['map_id' => null]);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->isSuccessful();

        $this->assertDatabaseMissing((new Upload)->getTable(), ['uploadable_type' => 'App\Question']);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout']]);
    }

    /** @test */
    public function a_template_must_be_a_type_of_image_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id], 1, false);

        $data = factory(Question::class)->raw(['map_id' => null]);

        $data['template'] = 'Some text';

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('template')[0], 'The template must be an image.');

        $this->assertDatabaseMissing((new Upload)->getTable(), ['uploadable_type' => 'App\Question']);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $question->callout]);
    }

    /** @test */
    public function template_size_cannot_be_greater_then_x_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id], 1, false);

        $data = factory(Question::class)->raw(['map_id' => null]);

        $data['template'] = UploadedFile::fake()->image('template.jpg')->size(($this->question_max_template_size + 1));

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('template')[0], 'The template may not be greater than 15000 kilobytes.');

        $this->assertDatabaseMissing((new Upload)->getTable(), ['uploadable_type' => 'App\Question']);
        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $question->callout]);
    }

    /** @test */
    public function delete_old_template_when_a_user_updates_a_new_template_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id], 1, true, ['name' => 'old.jpg']);

        $oldTemplate = $question->template()->first();

        $data = factory(Question::class)->raw(['map_id' => null]);

        $data['template'] = UploadedFile::fake()->image('new.jpg')->size($this->question_max_template_size);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->isSuccessful();

        $this->assertDatabaseHas((new Question)->getTable(), ['callout' => $data['callout']]);
        $this->assertDatabaseHas((new Upload)->getTable(), ['uploadable_type' => 'App\Question', 'name' => 'new.jpg', 'deleted_at' => null]);
        $this->assertNotEquals($oldTemplate->fresh()->deleted_at, null);
    }

    /** @test */
    public function unpublish_the_map_when_less_than_4_questions_are_published_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap(['published' => 1]);

        $question = $this->createQuestion(['map_id' => $map->id, 'published' => 1], 4)
            ->first();

        $data = factory(Question::class)->raw(['map_id' => null, 'published' => 0]);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->isSuccessful();

        $this->assertEquals(false, $map->fresh()->published);
    }

    /** @test */
    public function do_not_unpublish_the_map_when_more_than_4_questions_are_still_published_while_updating_a_question()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap(['published' => 1]);

        $question = $this->createQuestion(['map_id' => $map->id, 'published' => 1], 5)
            ->first();

        $data = factory(Question::class)->raw(['map_id' => null, 'published' => 0]);

        $this->patch(route('question.update', ['map' => $map->id, 'question' => $question->id]), $data)->isSuccessful();

        $this->assertEquals(true, $map->fresh()->published);
    }
}
