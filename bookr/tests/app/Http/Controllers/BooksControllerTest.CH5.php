<?php

namespace Tests\App\Http\Controllers;

use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

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
        $this->get('/books')
             ->seeJson([
                 'title' => 'War of the Worlds'
                ])
             ->seeJson([
                 'title' => 'A Wrinkle in Time'
                ]);
    }
    
    /** @test **/
    public function show_should_return_a_valid_book()
    {
        // $this->markTestIncomplete('Pending test');
        $this
            ->get('/books/1')
            ->seeStatusCode(200)
            ->seeJson([
                'id' => 1,
                'title' => 'War of the Worlds',
                'description' => 'A science fiction masterpiece about Martians invading London',
                'author' => 'H. G. Wells'
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
        // $this->markTestIncomplete('pending');

        $this->notSeeInDatabase('books', [
                'title' => 'The War of the Worlds'
        ]);

        $this->put('/books/1', [
            'id' => 5,
            'title' => 'The War of the Worlds',
            'description' => 'The book is way better than the movie.',
            'author' => 'Wells, H. G.'
        ]);

        $this
            ->seeStatusCode(200)
            ->seeJson([
                'id' => 1,
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
        // $this->markTestIncomplete('pending');
        $this
            ->delete('/books/1')
            ->seeStatusCode(204)
            ->isEmpty();

        $this->notSeeInDatabase('books', ['id' => 1]);
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
