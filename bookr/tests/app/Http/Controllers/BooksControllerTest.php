<?php

namespace Tests\App\Http\Controllers;

use TestCase;

class BooksControllerTest extends TestCase
{
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
        $this->markTestIncomplete('Pending test');
    }
}
