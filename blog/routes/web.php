<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->app->version();
});

$app->get('/hello/world', function () use ($app) {
    return "Hello world!";
});

$app->get('/hello/{name}', ['middleware' => 'hello', function ($name) {
    return "Hello {$name}";
}]);

$app->get('/request', function (Illuminate\Http\Request $request) {
    return "Hello " . $request->get('name', 'stranger');
});

$app->get('/response', function (Illuminate\Http\Request $request) {
    if ($request->wantsJson()) {
        return response()->json(['greeting' => 'Hello stranger']);
    }

    // return (new Illuminate\Http\Response('Hello stranger', 200))
    //     ->header('Content-Type', 'text/plain');
    return response()
        ->make('Hello stranger', 200, ['Content-Type' => 'text/plain']);
});
