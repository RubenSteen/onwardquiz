<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function signIn($data = null)
    {
        if (is_null($data)) {
            $user = $this->actingAs(factory(User::class)->create());
        } elseif (is_array($data)) {
            $user = $this->actingAs(factory(User::class)->create($data));
        } elseif ($data instanceof User) {
            $user = $this->actingAs($data);
        } else {
            throw new \Exception('Given data not supported for signIn method');
        }

        return $user;
    }
}
