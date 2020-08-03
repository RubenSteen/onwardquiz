<?php

namespace Tests\Feature\MapTests;

use App\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Map;

class MapUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_update_a_map()
    {
        $map = $this->createMap();

        $this->signIn();

        $this->patch(route('map.update', $map->id), ['name' => 'Some other name'])->assertForbidden();

        $this->assertDatabaseHas((new Map())->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function a_superadmin_can_update_a_map()
    {
        $map = $this->createMap();

        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw(['name' => 'Some Other name']))
            ->assertRedirect(route('map.edit', $map->id));

        $this->assertDatabaseMissing((new Map())->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function a_editor_can_update_a_map()
    {
        $map = $this->createMap();

        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw(['name' => 'Some Other name']))
            ->assertRedirect(route('map.edit', $map->id));

        $this->assertDatabaseMissing((new Map())->getTable(), ['name' => $map->name]);
    }

    /** @test */
    public function name_is_required_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        // Data that will be send
        $data = [
            'name' => '',
        ];

        $map = $this->createMap();

        $this->patch(route('map.update', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('name')[0], "The name field is required.");
    }

    /** @test */
    public function name_is_unique_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $existingMap = $this->createMap();
        $updateMap = $this->createMap();

        // Data that will be send
        $data = [
            'name' => $existingMap->name,
        ];

        $this->patch(route('map.update', $updateMap->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('name')[0], "The name has already been taken.");
    }

    /** @test */
    public function published_is_required_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        // Data that will be send
        $data = [
            'published' => '',
        ];

        $map = $this->createMap();

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('published')[0], "The published field is required.");
    }

    private $min_questions_published = 4;

    /** @test */
    public function cannot_publish_a_map_if_it_does_not_have_x_questions_published_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = [
            'published' => true,
        ];

        $map = $this->createMap();

        for ($i = 0; $i < $this->min_questions_published; $i++) {
            $map->questions()->create(factory(Question::class)->raw(['published' => false, 'map_id' => $map->id]));
        }

        $this->assertCount($this->min_questions_published, Question::all());

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('published')[0],"Cannot be published, the map requires to have a minimum of 4 questions published");
    }

    /** @test */
    public function can_publish_a_map_if_it_does_have_x_questions_published_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = [
            'published' => true,
        ];

        $map = $this->createMap();

        for ($i = 0; $i < $this->min_questions_published; $i++) {
            $map->questions()->create(factory(Question::class)->raw(['published' => true, 'map_id' => $map->id]));
        }

        $this->assertCount($this->min_questions_published, Question::where('published', true)->get());

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->isSuccessful();

        $this->assertDatabaseHas((new Map())->getTable(), ['published' => true]);
    }

    /** @test */
    public function description_is_nullable_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = [
            'description' => '',
        ];

        $map = $this->createMap(['description' => 'Something']);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->isSuccessful();

        $this->assertDatabaseHas((new Map())->getTable(), ['description' => null]);
    }

    /** @test */
    public function description_can_be_set_to_a_string_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = [
            'description' => 'Some other text',
        ];

        $map = $this->createMap(['description' => 'Something']);

        $this->patch(route('map.update', $map->id), factory(Map::class)->raw($data))->isSuccessful();

        $this->assertDatabaseMissing((new Map())->getTable(), ['description' => $map->description]);
    }

    /** @test */
    public function template_is_nullable_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = array_merge(factory(Map::class)->raw(), ['template' => '']);

        $map = $this->createMap([], 1, false);

        $this->patch(route('map.update', $map->id), $data)->isSuccessful();

        $this->assertEquals(0, $map->template()->count());
        $this->assertDatabaseCount((new Map())->getTable(), 1);
    }

    /** @test */
    public function template_must_be_a_type_of_image_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = array_merge(factory(Map::class)->raw(), ['template' => 'some text']);

        $map = $this->createMap();

        $this->patch(route('map.update', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('template')[0],"The template must be an image.");
    }

    private $map_max_template_size = 15000;

    /** @test */
    public function a_template_can_be_uploaded_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw();

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->map_max_template_size);

        $map = $this->createMap();

        $this->patch(route('map.update', $map->id), $data)->isSuccessful();

        $this->assertEquals(1, $map->template()->count());
        $this->assertDatabaseCount((new Map())->getTable(), 1);
    }

    /** @test */
    public function delete_old_template_when_a_user_adds_a_new_one_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw();

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->map_max_template_size);

        $map = $this->createMap();

        $this->patch(route('map.update', $map->id), $data)->isSuccessful();

        $this->assertEquals(1, $map->template()->count());
        $this->assertEquals(2, $map->template()->withTrashed()->count());
        $this->assertDatabaseCount((new Map())->getTable(), 1);
    }

    /** @test */
    public function template_size_cannot_be_greater_then_x_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw();

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size(($this->map_max_template_size + 1));

        $map = $this->createMap();

        $this->patch(route('map.update', $map->id), $data)->assertSessionHasErrors();

        $this->assertEquals(session('errors')->get('template')[0],"The template may not be greater than 15000 kilobytes.");
    }

    /** @test */
    public function when_a_template_gets_modified_the_questions_that_are_linked_to_the_map_will_all_be_unpublished_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw();

        $data['template'] = UploadedFile::fake()->image('avatar.jpg')->size($this->map_max_template_size);

        $map = $this->createMap();

        $this->createQuestion(['published' => true, 'map_id' => $map->id], 10);

        $this->patch(route('map.update', $map->id), $data)->isSuccessful();

        $this->assertDatabaseMissing((new Question())->getTable(), ['published' => true]);
    }

    /** @test */
    public function when_a_template_does_not_get_modified_the_questions_that_are_linked_to_the_map_will_not_be_changed_while_updating_a_map()
    {
        $this->signIn(['editor' => true]);

        $data = factory(Map::class)->raw();

        $map = $this->createMap();

        $this->createQuestion(['published' => true, 'map_id' => $map->id], 5);
        $this->createQuestion(['published' => false, 'map_id' => $map->id], 5);

        $questions = $map->fresh()->questions;

        $this->assertCount(10, $questions);

        $this->patch(route('map.update', $map->id), $data)->isSuccessful();

        foreach ($questions as $question) {
            $this->assertEquals($question->published, Question::find($question->id)->published);
        }
    }
}