<?php

namespace Tests\Feature\QuestionTests;

use App\Map;
use App\Question;
use App\Upload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_view_the_edit_page()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $this->get(route('question.edit', ['map' => $map->id, 'question' => $question->id]))->assertForbidden();
    }

    /** @test */
    public function a_editor_can_view_the_edit_page()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $response = $this->get(route('question.edit', ['map' => $map->id, 'question' => $question->id]));

        $this->assertEquals($response->props()['question']['callout'], $question->callout);

        $response->assertStatus(200);
    }

    /** @test */
    public function a_superadmin_can_view_the_edit_page()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $response = $this->get(route('question.edit', ['map' => $map->id, 'question' => $question->id]));

        $this->assertEquals($response->props()['question']['callout'], $question->callout);

        $response->assertStatus(200);
    }

    /** @test */
    public function cannot_view_the_edit_page_when_no_map_template_is_available()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $map = $this->createMap([], 1, false);

        $question = $this->createQuestion(['map_id' => $map->id]);

        $this->get(route('question.edit', ['map' => $map->id, 'question' => $question->id]))->assertStatus(403);
    }

    /** @test */
    public function cannot_edit_a_question_when_the_map_is_not_the_given_map()
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
}
