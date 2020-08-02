<?php

namespace Tests;

use App\Map;
use App\Question;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Illuminate\Support\Arr;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('props', function ($key = null) {
            $props = json_decode(json_encode($this->original->getData()['page']['props']), JSON_OBJECT_AS_ARRAY);

            if ($key) {
                return Arr::get($props, $key);
            }

            return $props;
        });

        TestResponse::macro('assertHasProp', function ($key) {
            Assert::assertTrue(Arr::has($this->props(), $key));

            return $this;
        });

        TestResponse::macro('assertPropValue', function ($key, $value) {
            $this->assertHasProp($key);

            if (is_callable($value)) {
                $value($this->props($key));
            } else {
                Assert::assertEquals($this->props($key), $value);
            }

            return $this;
        });

        TestResponse::macro('assertPropCount', function ($key, $count) {
            $this->assertHasProp($key);

            Assert::assertCount($count, $this->props($key));

            return $this;
        });
    }

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

    protected function createMap($overrides = [], $amount = 1)
    {
        $data = factory(Map::class, $amount)->create($overrides);

        if ($amount > 1) {
            return $data;
        }

        return $data->first();
    }

    protected function createQuestion($overrides = [], $amount = 1)
    {
        $data = factory(Question::class, $amount)->create($overrides);

        if ($amount > 1) {
            return $data;
        }

        return $data->first();
    }
}
