<?php

namespace Tests\App\Http\Controllers;

use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Log;

class BooksControllerTest extends TestCase
{
    /*that you’ve see the first example of using a factory,
    you need a way to migrate and reset your database
    before each test requiring the database*/
    use DatabaseMigrations;

    //Requesting an Individual book
    /** @test **/
    public function index_status_code_should_be_200()
    {
        $this->get('/books')->seeStatusCode(200);
    }

    /** @test **/
    public function index_should_return_a_collection_of_records()
    {
        /*Responding to Errors - Factories in Tests*/
        /*
        you want to use the App\Book factory, and that you want it to
        generate two book models. The second argument is optional,
        and if you omit it you will get one record back.
        */
        $books = factory('App\Book', 2)->create();
        
        $this->get('/books');
        foreach ($books as $book) {
            $this->seeJson(['title' => $book->title]);
        }
    }
    
    /** @test **/
    public function show_should_return_a_valid_book()
    {
        /*Responding to Errors - Factories in Tests*/
        $book = factory('App\Book')->create();
        $this
            ->get("/books/{$book->id}")
            ->seeStatusCode(200)
            ->seeJson([
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'author' => $book->author
            ]);
        $data = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
    }

    /** @test **/
    public function show_should_fail_when_the_book_id_does_not_exist()
    {
        // $this->markTestIncomplete('Pending test');
        $this->get('/books/9999')
            ->seeStatusCode(404)
            ->seeJson([
                'error' => [
                    'message' => 'Book not found',
                ]
            ]);
    }
    
    /** @test **/
    public function show_route_should_not_match_an_invalid_route()
    {
        $this->get('/books/this-is-invalid');

        $this->assertNotRegExp(
            '/Book not found/',
            $this->response->getContent(),
            'BooksController@show route matching when it should not.'
        );
        // $this->markTestIncomplete('Pending test');
    }

    // Creating a new book
    /** @test **/
    public function store_should_save_new_book_in_the_database()
    {
        // $this->markTestIncomplete('pending');
        $this->post('/books', [
            'title' => 'The Invisible Man',
            'description' => 'An invisible man is trapped in the terror of his own creation',
            'author' => 'H. G. Wells'
        ]);

        $this
            ->seeJson(['created' => true])
            ->seeInDatabase('books', ['title' => 'The Invisible Man']);
    }

    /** @test */
    public function store_should_respond_with_a_201_and_location_header_when_successful()
    {
        // $this->markTestIncomplete('pending');
  
        $this->post('/books', [
            'title' => 'The Invisible Man',
            'description' => 'An invisible man is trapped in the terror of his own creation',
            'author' => 'H. G. Wells'
        ]);
        $this
            ->seeStatusCode(201)
            ->seeHeaderWithRegExp('Location', '#/books/[\d]+$#');
    }


    // Update existing books
    /** @test **/
    public function update_should_only_change_fillable_fields()
    {
        /*Responding to Errors - Factories in Tests*/
        $book = factory('App\Book')->create([
            'title' => 'War of the Worlds',
            'description' => 'A science fiction masterpiece about Martians invading London',
            'author' => 'H. G. Wells',
        ]);

        //  using a model factory to test that only fillable fields can be updated
        $this
            ->put("/books/{$book->id}", [
                'id' => 5,
                'title' => 'The War of the Worlds',
                'description' => 'The book is way better than the movie.',
                'author' => 'Wells, H. G.'
            ]);

        $this
            ->seeStatusCode(200)
            ->seeJson([
                'id' =>1,
                'title' => 'The War of the Worlds',
                'description' => 'The book is way better than the movie.',
                'author' => 'Wells, H. G.'
            ])
            ->seeInDatabase('books', [
                'title' => 'The War of the Worlds'
            ]);
    }

    /** @test **/
    public function update_should_fail_with_an_invalid_id()
    {
        $this->put('books/99999999999999')
            ->seeStatusCode(404)
            ->seeJsonEquals([
                'error' => [
                    'message' => 'Book not found'
                ]
            ]);
        // $this->markTestIncomplete('pending');
    }

    /** @test **/
    public function update_should_not_match_an_invalid_route()
    {
        $this->put('/books/this-is-invalid')
            ->seeStatusCode(404);
        // $this->markTestIncomplete('pending');
    }

    //Deleting Books

    /** @test **/
    public function destroy_should_remove_a_valid_book()
    {
        /*Responding to Errors - Factories in Tests*/
        $book = factory('App\Book')->create();

        $this
            ->delete("/books/{$book->id}");

        $this
            ->seeStatusCode(204)
            ->isEmpty();
        
        $this->notSeeInDatabase('books', ['id' => $book->id]);
    }

    /** @test **/
    public function destroy_should_return_a_404_with_an_invalid_id()
    {
        // $this->markTestIncomplete('pending');
        $this
            ->delete('/books/999999999')
            ->seeStatusCode(404)
            ->seeJsonEquals(
                [
                    'error' => [
                        'message' => 'Book not found'
                    ]
                ]
            );
    }

    /** @test **/
    public function destroy_should_not_match_an_invalid_route()
    {
        // $this->markTestIncomplete('pending');
        $this->delete('/books/this-is-valid')
            ->seeStatusCode(404);
    }
}
