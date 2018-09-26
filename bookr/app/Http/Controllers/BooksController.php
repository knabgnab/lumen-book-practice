<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use App\Transformer\BookTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/** Chapter 8 Validation
* Class BooksController
* @package App\Http\Controllers
*/
class BooksController extends Controller
{
    /*
    GET /books
    @return array
    */

    public function index()
    {
        return $this->collection(Book::all(), new BookTransformer());
        // return ['data' => Book::all()->toArray()];
    }

    /**
    * GET /books/{id}
    * @param integer $id
    * @return mixed
    */
    public function show($id)
    {
        return $this->item(Book::findOrFail($id), new BookTransformer());
        // return  ['data' => Book::findOrFail($id)];
    }

    /** Chapter 8 - Validation
     * POST /books
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'author_id' => 'required|exists:authors,id'
        ]);
        $book = Book::create($request->all());
        $data = $this->item($book, new BookTransformer());

        return response()->json($data, 201, [
            'Location' => route('books.show', ['id' => $book->id])
        ]);
    }

    /**
     * PUT /books/{id}
    *
    * @param Request $request
    * @param $id
    * @return mixed
    */
    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }
        
        $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'author_id' => 'exists:authors,id', [
                'description.required' => 'Please fill out the :attribute.'
            ]
        ]);
        $book->fill($request->all());
        $book->save();

        return $this->item($book, new BookTransformer());
        // return ['data' => $book->toArray()];
    }

    /**
     * DELETE /books/{id}
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }

        $book->delete();
        
        return response(null, 204);
    }
}
