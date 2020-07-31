<?php

namespace Tests\Feature\MapTests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_authenticated_user_cannot_view_the_index()
    {
        $this->signIn(['super_admin' => false, 'editor' => false]);

        $this->get(route('map.index'))->assertForbidden();
    }

    /** @test */
    public function a_superadmin_can_view_the_index()
    {
        $this->signIn(['super_admin' => true, 'editor' => false]);

        $this->get(route('map.index'))->assertStatus(200);
    }

    /** @test */
    public function a_editor_can_view_the_index()
    {
        $this->signIn(['super_admin' => false, 'editor' => true]);

        $this->get(route('map.index'))->assertStatus(200);
    }
}
