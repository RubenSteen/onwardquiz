<?php

namespace Tests\Feature\QuestionTests;

use App\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_view_the_create_page()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $map = $this->createMap()->template()->create(factory(Upload::class)->raw());

        $this->get(route('question.create', $map))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_view_the_create_page()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $map = $this->createMap();

        $map->template()->create(factory(Upload::class)->raw());

        $data = $this->get(route('question.create', $map))->assertStatus(200)->props();

        $this->assertEquals($data['map']['name'], $map->name);
    }

    /** @test */
    public function a_editor_can_view_the_create_page()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $map = $this->createMap();

        $map->template()->create(factory(Upload::class)->raw());

        $data = $this->get(route('question.create', $map))->assertStatus(200)->props();

        $this->assertEquals($data['map']['name'], $map->name);
    }

    /** @test */
    public function cannot_view_the_create_page_when_no_map_template_is_available()
    {
        $this->signIn(['editor' => true]);

        $map = $this->createMap();

        $this->get(route('question.create', $map))->assertStatus(403);
    }
}
