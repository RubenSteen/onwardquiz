<?php

namespace Tests\Feature\QuestionTests;

use App\Upload;
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
}
