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

        $map->template()->create(factory(Upload::class)->raw());

        $question = $this->createQuestion(['map_id' => $map->id]);

        $question->template()->create(factory(Upload::class)->raw());

        $this->get(route('question.edit', ['map' => $map->id, 'question' => $question->id]))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_view_the_edit_page()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $map = $this->createMap();

        $map->template()->create(factory(Upload::class)->raw());

        $question = $this->createQuestion(['map_id' => $map->id]);

        $question->template()->create(factory(Upload::class)->raw());

        $data = $this->get(route('question.edit', ['map' => $map->id, 'question' => $question->id]))
            ->assertStatus(200)
            ->props();

        $this->assertEquals($data['question']['callout'], $question->callout);
    }

    /** @test */
    public function cannot_view_the_edit_page_when_no_map_template_is_available()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $map = $this->createMap();

        $question = $this->createQuestion(['map_id' => $map->id]);

        $question->template()->create(factory(Upload::class)->raw());

        $this->get(route('question.edit', ['map' => $map->id, 'question' => $question->id]))->assertStatus(403);
    }
}
