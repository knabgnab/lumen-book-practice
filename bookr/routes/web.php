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

$app->get('/books', 'BooksController@index');
$app->get('/books/{id:[\d]+}', [
    'as' => 'books.show',
    'uses' => 'BooksController@show'
]);
$app->post('/books', 'BooksController@store');
$app->put('/books/{id:[\d]+}', 'BooksController@update');
$app->delete('/books/{id:[\d]+}', 'BooksController@destroy');

$app->group([ //Chapter 10
    'prefix' => '/authors',
    // 'namespace' => 'App\Http\Controllers'
], function (\Laravel\Lumen\Routing\Router $app) {
    $app->get('/', 'AuthorsController@index');
    $app->post('/', 'AuthorsController@store');
    $app->get('/{id:[\d]+}', 'AuthorsController@show');
    $app->put('/{id:[\d]+}', [
        'as' => 'authors.show',
        'uses' => 'AuthorsController@update'
    ]);
    // $app->delete('/{id:[\d]+}', 'AuthorsController@destroy');

    // Author ratings
    $app->post('/{id:[\d]+}/ratings', 'AuthorsRatingsController@store');
    $app->delete(
        '/{authorId:[\d]+}/ratings/{ratingId:[\d]+}',
        'AuthorsRatingsController@destroy'
    );
});

$app->group([
    'prefix' => '/bundles',
    // 'namespace' => 'App\Http\Controllers'
], function (\Laravel\Lumen\Routing\Router $app) {
    $app->get('/{id:[\d]+}', [
        'as' => 'bundles.show',
        'uses' => 'BundlesController@show'
    ]);

    $app->put(
        '/{bundleId:[\d]+}/books/{bookId:[\d]+}',
        'BundlesController@addBook'
    );

    $app->delete(
        '/{bundleId:[\d]+}/books/{bookId:[\d]+}',
        'BundlesController@removeBook'
    );
});
