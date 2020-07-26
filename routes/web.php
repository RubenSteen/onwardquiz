<?php

/*
|--------------------------------------------------------------------------
| Best practice
|--------------------------------------------------------------------------
|
| Route::get('users', ['as' => 'user.index',    'uses' => 'UserController@index']);
| Route::get('user/create', ['as' => 'user.create', 'uses' => 'UserController@create']);
| Route::post('user', ['as' => 'user.store',    'uses' => 'UserController@store']);
| Route::get('user/{user_id}', ['as' => 'user.show',    'uses' => 'UserController@show']);
| Route::get('user/{user_id}/edit', ['as' => 'user.edit',   'uses' => 'UserController@edit']);
| Route::patch('user/{user_id}', ['as' => 'user.update',    'uses' => 'UserController@update']);
| Route::delete('user/{user_id}', ['as' => 'user.delete',   'uses' => 'UserController@delete']);
|
*/

Route::get('/', ['as' => 'home',    'uses' => 'QuizController@landing']);


if (App::environment('production', 'staging')) {
    Route::get('login/discord', 'Auth\AuthController@login')->name('login')->middleware('guest');
} else {
    Route::get('login/devenv', ['as' => 'login',    'uses' => 'Auth\DevEnv\AuthController@showLogin', 'middleware' => 'guest']);
    Route::post('login/devenv', ['as' => 'login',   'uses' => 'Auth\DevEnv\AuthController@login', 'middleware' => 'guest']);
}


Route::group(['middleware' => ['auth', 'last.activity']], function ($router) {
    // A tiny bit of Javacript does a request every 5 minutes to trigger the last activity middleware, to update the user.
    Route::post('/activity-check', ['uses' => 'QuizController@activityCheck']);
    
    Route::post('logout', [ 'as' => 'logout.post', 'uses' => 'Auth\AuthController@logout']);

    /*
    |--------------------------------------------------------------------------
    | Quiz Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::get('quiz', ['as' => 'quiz.index',   'uses' => 'QuizController@index']);
    Route::get('quiz/{map:id}', ['as' => 'quiz.show',   'uses' => 'QuizController@show']);
    Route::post('quiz/{map:id}/question', ['as' => 'quiz.get-question', 'uses' => 'QuizController@getQuestion']);
    Route::post('quiz/{map:id}/{question:id}/check', ['as' => 'quiz.check-answer',  'uses' => 'QuizController@checkAnswer']);

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::get('profile', ['as' => 'user.profile',  'uses' => 'UserController@index']);



    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes
    | Prefix the namespace, routes, named routes with 'admin'
    | Also adds the middleware superadmin
    |--------------------------------------------------------------------------
    |
    */
    Route::group(['middleware' => ['superadmin'], 'namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'], function ($router) {
        Route::get('', ['as' => 'admin.index',  'uses' => 'AdminController@index']);

        /*
        |--------------------------------------------------------------------------
        | Map Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::get('maps', ['as' => 'map.index',    'uses' => 'MapController@index']);
        Route::get('map/create', ['as' => 'map.create', 'uses' => 'MapController@create']);
        Route::post('map', ['as' => 'map.store',    'uses' => 'MapController@store']);
        Route::get('map/{map_id}/edit', ['as' => 'map.edit',    'uses' => 'MapController@edit']);
        Route::patch('map/{map_id}', ['as' => 'map.update', 'uses' => 'MapController@update']);
        Route::post('map/{map_id}/image-validation', ['as' => 'map.image-validation',   'uses' => 'MapController@imageValidation']);

        /*
        |--------------------------------------------------------------------------
        | Map Question Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::get('map/{map:id}/question/create', ['as' => 'question.create',  'uses' => 'QuestionController@create']);
        Route::post('map/{map:id}/question', ['as' => 'question.store', 'uses' => 'QuestionController@store']);
        Route::get('map/{map:id}/question/{question:id}/edit', ['as' => 'question.edit',    'uses' => 'QuestionController@edit']);
        Route::patch('map/{map:id}/question/{question:id}', ['as' => 'question.update', 'uses' => 'QuestionController@update']);
        Route::post('map/{map:id}/question/{question:id}/image', ['as' => 'question.store.image',   'uses' => 'QuestionController@storeImage']);
        Route::post('map/{map:id}/question/{question:id}/{picture:id}/image', ['as' => 'question.edit.image',   'uses' => 'QuestionController@editImage']);
        Route::delete('map/{map:id}/question/{question:id}/{picture:id}/image', ['as' => 'question.delete.image',   'uses' => 'QuestionController@deleteImage']);

        /*
        |--------------------------------------------------------------------------
        | User Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::get('users', ['as' => 'user.index',  'uses' => 'UserController@index']);
        Route::get('user/{user_id}/edit', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
        Route::patch('user/{user:id}', ['as' => 'user.update',  'uses' => 'UserController@update']);

        /*
        |--------------------------------------------------------------------------
        | Team Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::get('teams', ['as' => 'team.index',  'uses' => 'TeamController@index']);
    });
});
