<?php

namespace App\Http\Controllers;

use App\Book;

/**
* Class BooksController
* @package App\Http\Controllers
*/
class BooksController
{

/*
 GET /books
 @return array
*/

    public function index()
    {
        return Book::all();
        // // return [];
        // return [
        //     ['title' => 'War of the Worlds'],
        //     ['title' => 'A Wrinkle in Time']
        // ];
    }

    /**
    * GET /books/{id}
    * @param integer $id
    * @return mixed
    */
    public function show($id)
    {
        try {
            return Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                        'error' => [
                            'message' => 'Book not found'
                        ]
                ], 404);
        }
    }
}
