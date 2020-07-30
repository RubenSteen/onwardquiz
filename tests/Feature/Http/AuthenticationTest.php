<?php

namespace Tests\Feature\Http;

use Tests\TestCase;
use Tests\Feature\Http\HttpSetup;

class AuthenticationTest extends HttpSetup
{


    /**
     * @test
     * @dataProvider protectedRoutesProvider
     */
    public function guests_are_prevented_by_unauthenticated_exception($method, $url, $authenticated = true, $status = null)
    {
        $this->assertGuest();

        if ($authenticated === true) {
            $this->withoutExceptionHandling();

            $this->expectExceptionMessage('Unauthenticated.');
        }

        $response = $this->$method($url)->assertStatus($status ?: 200);
    }

}
