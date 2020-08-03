<?php

namespace Tests;

use App\Map;
use App\Question;
use App\QuestionPicture;
use App\Upload;
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

    protected function createMap($overrides = [], $amount = 1, $createTemplate = true, $templateOverrides = [])
    {
        $maps = factory(Map::class, $amount)->create($overrides);

        if ($createTemplate === true) {
            foreach ($maps as $map) {
                $map->template()->create(factory(Upload::class)->raw($templateOverrides));
            }
        }

        if ($amount > 1) {
            return $maps;
        }

        return $maps->first();
    }

    protected function createQuestion($overrides = [], $amount = 1, $createTemplate = true, $templateOverrides = [])
    {
        $questions = factory(Question::class, $amount)->create($overrides);

        if ($createTemplate === true) {
            foreach ($questions as $question) {
                $question->template()->create(factory(Upload::class)->raw($templateOverrides));
            }
        }

        if ($amount > 1) {
            return $questions;
        }

        return $questions->first();
    }

    protected function createQuestionPicture($overrides = [], $amount = 1, $createTemplate = true, $templateOverrides = [])
    {
        $questionPictures = factory(QuestionPicture::class, $amount)->create($overrides);

        if ($createTemplate === true) {
            foreach ($questionPictures as $picture) {
                $picture->image()->create(factory(Upload::class)->raw($templateOverrides));
            }
        }

        if ($amount > 1) {
            return $questionPictures;
        }

        return $questionPictures->first();
    }
}
