<?php

namespace Tests\Feature\Http;

use Tests\TestCase;

class HttpSetup extends TestCase
{
    public $excludeRoutes = [
        '_debugbar/open',
        '_debugbar/cache/{key}/{tags?}',
        '_debugbar/clockwork/{id}',
        '_debugbar/telescope/{id}',
        '_debugbar/assets/stylesheets',
        '_debugbar/assets/javascript',
    ];

    public function protectedRoutesProvider()
    {
        return [
            // Counting starts at 0
            [
                'method' => 'GET',
                'url' => '/',
                'authenticated' => false,
                'assertStatus' => 200,
            ],
            [
                'method' => 'GET',
                'url' => 'login/discord',
                'authenticated' => false,
                'assertStatus' => 302,
            ],

            [
                'method' => 'GET',
                'url' => 'maps',
            ],
            [
                'method' => 'GET',
                'url' => 'map/create',
            ],
            [
                'method' => 'GET',
                'url' => 'map/{map_id}/edit',
            ],
            [
                'method' => 'GET',
                'url' => 'map/{map}/question/create',
            ],

            [
                'method' => 'GET',
                'url' => 'map/{map}/question/{question}/edit',
            ],
            [
                'method' => 'GET',
                'url' => 'quiz',
            ],
            [
                'method' => 'GET',
                'url' => 'quiz/{map}',
            ],
            [
                'method' => 'GET',
                'url' => 'profile',
            ],
            [
                'method' => 'GET',
                'url' => 'admin',
            ],
            [
                'method' => 'GET',
                'url' => 'admin/users',
            ],
            [
                'method' => 'GET',
                'url' => 'admin/user/{user_id}/edit',
            ],
            [
                'method' => 'DELETE',
                'url' => 'map/{map}',
            ],
            [
                'method' => 'DELETE',
                'url' => 'map/{map}/question/{question}/{picture}/picture',
            ],
            [
                'method' => 'POST',
                'url' => 'activity-check',
            ],
            [
                'method' => 'POST',
                'url' => 'logout',
            ],
            [
                'method' => 'POST',
                'url' => 'map',
            ],
            [
                'method' => 'POST',
                'url' => 'map/{map_id}/image-validation',
            ],
            [
                'method' => 'POST',
                'url' => 'map/{map}/question',
            ],
            [
                'method' => 'POST',
                'url' => 'map/{map}/question/{question}/picture',
            ],
            [
                'method' => 'POST',
                'url' => 'map/{map}/question/{question}/{picture}/picture',
            ],
            [
                'method' => 'POST',
                'url' => 'quiz/{map}/question',
            ],
            [
                'method' => 'POST',
                'url' => 'quiz/{map}/{question}/check',
            ],
            [
                'method' => 'PATCH',
                'url' => 'map/{map}',
            ],
            [
                'method' => 'PATCH',
                'url' => 'map/{map}/question/{question}',
            ],
            [
                'method' => 'PATCH',
                'url' => 'admin/user/{user}',
            ],
            [
                'method' => 'PUT',
                'url' => 'map/{map_id}',
            ],
            [
                'method' => 'PUT',
                'url' => 'map/{map}/question/{question}/{picture}/picture',
            ],
            [
                'method' => 'DELETE',
                'url' => 'map/{map}/question/{question}',
            ],
            [
                'method' => 'PUT',
                'url' => 'map/{map}/question/{question_id}',
            ],
            [
                'method' => 'PUT',
                'url' => 'map/{map}/question/{question}/{picture_id}/picture',
            ],
        ];
    }

    /**
     * @test
     */
    public function every_route_is_registered_in_the_protectedRoutesProvider_method()
    {
        $routeCollection = $this->getRoutes();

        foreach ($routeCollection as $method => $routes) {
            foreach ($routes as $url => $data) {
                if (in_array($url, $this->excludeRoutes)) {
                    continue;
                }

                $routesArrayByMethod = $this->search($this->protectedRoutesProvider(), 'method', $method);

                $routesArrayByMethodAndUrl = $this->search($routesArrayByMethod, 'url', $url);

                if (count($routesArrayByMethodAndUrl) === 0) {
                    throw new \Exception("Route URL '$url' / Method '$method' - does not exist");
                } elseif (count($routesArrayByMethodAndUrl) > 1) {
                    throw new \Exception("Route URL '$url' / Method '$method' - has too many registered in the array");
                }

                $this->assertCount(1, $routesArrayByMethodAndUrl);
            }
        }
    }

    protected function getRoutes()
    {
        $seenRoutes = [];

        foreach (\Route::getRoutes() as $routeCollection) {
            foreach ($routeCollection->methods as $method) {
                if ($method === 'HEAD') {
                    continue;
                }

                // Create methods collection
                if (! array_key_exists($method, $seenRoutes)) {
                    $seenRoutes[$method] = [];
                }

                if (isset($seenRoutes[$method][$routeCollection->uri])) {
                    throw new \Exception("Route URL '$routeCollection->uri' already exists in the method array '$method'");
                }

                $seenRoutes[$method][$routeCollection->uri] = $routeCollection->action;
            }
        }

        return $seenRoutes;
    }

    protected function search($array, $key, $value)
    {
        $results = [];

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }

        return $results;
    }
}
