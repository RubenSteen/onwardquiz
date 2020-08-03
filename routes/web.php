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

Route::get('/', function () {
    return Inertia\Inertia::render('Landing');
})->name('home');

if (App::environment('production', 'staging', 'testing')) {
    Route::get('login/discord', 'Auth\AuthController@login')->name('login')->middleware('guest');
} else {
    Route::get('login/devenv', ['as' => 'login',    'uses' => 'Auth\DevEnv\AuthController@showLogin', 'middleware' => 'guest']);
    Route::post('login/devenv', ['as' => 'login',   'uses' => 'Auth\DevEnv\AuthController@login', 'middleware' => 'guest']);
}


Route::group(['middleware' => ['auth', 'last.activity']], function ($router) {
    // A tiny bit of Javacript does a request every 5 minutes to trigger the last activity middleware, to update the user.
    Route::post('/activity-check', function () {
        return json_encode('You are alive!');
    });
    Route::post('logout', [ 'as' => 'logout.post', 'uses' => 'Auth\AuthController@logout']);

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
    Route::patch('map/{map}', ['as' => 'map.update', 'uses' => 'MapController@update']);
    Route::delete('map/{map}', ['as' => 'map.destroy', 'uses' => 'MapController@destroy']);
    Route::put('map/{map_id}', ['as' => 'map.restore', 'uses' => 'MapController@restore']);
    Route::post('map/{map_id}/image-validation', ['as' => 'map.image-validation',   'uses' => 'MapController@imageValidation']);

    /*
    | Map Question Routes
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    |
    */
    Route::get('map/{map:id}/question/create', ['as' => 'question.create',  'uses' => 'QuestionController@create']);
    Route::post('map/{map:id}/question', ['as' => 'question.store', 'uses' => 'QuestionController@store']);
    Route::get('map/{map:id}/question/{question:id}/edit', ['as' => 'question.edit',    'uses' => 'QuestionController@edit']);
    Route::patch('map/{map:id}/question/{question:id}', ['as' => 'question.update', 'uses' => 'QuestionController@update']);
    Route::delete('map/{map:id}/question/{question:id}', ['as' => 'question.destroy', 'uses' => 'QuestionController@destroy']);
    Route::post('map/{map:id}/question/{question:id}/picture', ['as' => 'question.store.picture',   'uses' => 'QuestionController@storePicture']);
    Route::post('map/{map:id}/question/{question:id}/{picture:id}/picture', ['as' => 'question.update.picture',   'uses' => 'QuestionController@updatePicture']);
    Route::delete('map/{map:id}/question/{question:id}/{picture:id}/picture', ['as' => 'question.delete.destroy',   'uses' => 'QuestionController@destroyPicture']);
    Route::put('map/{map:id}/question/{question:id}/{picture:id}/picture', ['as' => 'question.restore.picture',   'uses' => 'QuestionController@restorePicture']);

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
        Route::get('', ['as' => 'admin.dashboard',  'uses' => 'AdminController@dashboard']);
        /*
        |--------------------------------------------------------------------------
        | User Routes
        |--------------------------------------------------------------------------
        |
        */
        Route::get('users', ['as' => 'user.index',  'uses' => 'UserController@index']);
        Route::get('user/{user_id}/edit', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
        Route::patch('user/{user:id}', ['as' => 'user.update',  'uses' => 'UserController@update']);
    });
});
