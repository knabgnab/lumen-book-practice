<?php

namespace Tests\App\Http\Controllers;

use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

use Log;
use Carbon\Carbon;

class BooksControllerTest extends TestCase
{
    /*that you’ve see the first example of using a factory,
    you need a way to migrate and reset your database
    before each test requiring the database*/
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        
        Carbon::setTestNow(Carbon::now('UTC'));
    }

    public function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

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
        $books = $this->bookFactory(2);
        // $books = factory('App\Book', 2)->create();
        $this->get('/books');

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);

        foreach ($books as $book) {
            $this->seeJson([
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'author' => $book->author->name,
                'created' => $book->created_at->toIso8601String(),
                'updated' => $book->updated_at->toIso8601String(),
            ]);
        }
    }

    /** @test **/
    public function show_should_return_a_valid_book()
    {
        // $book = factory('App\Book')->create();
        $book = $this->bookFactory()->first();

        $this
            ->get("/books/{$book->id}")
            ->seeStatusCode(200);

        // Get the response and assert the data key exists
        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];

        // Assert the Book Properties match
        $this->assertEquals($book->id, $data['id']);
        $this->assertEquals($book->title, $data['title']);
        $this->assertEquals($book->description, $data['description']);
        $this->assertEquals($book->author->name, $data['author']);
        $this->assertEquals($book->created_at->toIso8601String(), $data['created']);
        $this->assertEquals($book->updated_at->toIso8601String(), $data['created']);
    }


    /** @test **/
    public function store_should_save_new_book_in_the_database()
    {
        $author = factory(\App\Author::class)->create([
            'name' => 'H. G. Wells'
        ]);
        $this->post('/books', [
            'title' => 'The Invisible Man',
            'description' => 'An invisible man is trapped in the terror of his own creation',
            'author_id' => $author->id
        ], ['Accept' => 'application/json']);
        $body = json_decode($this->response->getContent(), true);
        // dd($body);
        $this->assertArrayHasKey('data', $body);
        $data = $body['data'];
        $this->assertEquals('The Invisible Man', $data['title']);
        $this->assertEquals(
            'An invisible man is trapped in the terror of his own creation',
            $data['description']
        );
        $this->assertEquals('H. G. Wells', $data['author']);
        $this->assertTrue($data['id'] > 0, 'Expected a positive integer, but did not see one.');
        $this->assertArrayHasKey('created', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created']);
        $this->assertArrayHasKey('updated', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated']);
        $this->seeInDatabase('books', ['title' => 'The Invisible Man']);
    }

    /** @test */
    public function store_should_respond_with_a_201_and_location_header_when_successful()
    {
        $author = factory(\App\Author::class)->create();
        $this->post('/books', [
            'title' => 'The Invisible Man',
            'description' => 'An invisible man is trapped in the terror of his own creation',
            'author_id' => $author->id
        ], ['Accept' => 'application/json']);
    
        $this
            ->seeStatusCode(201)
            ->seeHeaderWithRegExp('Location', '#/books/[\d]+$#');
    }

    /** @test **/
    public function update_should_only_change_fillable_fields()
    {
        $book = $this->bookFactory()->first();
        // dd($book->first()->id);
        $this->notSeeInDatabase('books', [
            'title' => 'War of the Worlds',
            'description' => 'The book is way better than the movie.',
        ]);

        $this->put("/books/{$book->id}", [ 'id' => 5,
            'title' => 'The War of the Worlds',
            'description' => 'The book is way better than the movie.',
        ]);
        $this
        ->seeStatusCode(200)
        ->seeJson([
            'id' => 1,
            'title' => 'The War of the Worlds',
            'description' => 'The book is way better than the movie.',
        ])
        ->seeInDatabase('books', [
            'title' => 'The War of the Worlds'
        ]);
        $body = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $body);
        $data = $body['data'];
        $this->assertArrayHasKey('created', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['created']);
        $this->assertArrayHasKey('updated', $data);
        $this->assertEquals(Carbon::now()->toIso8601String(), $data['updated']);
    }

    /** @test **/
    public function destroy_should_remove_a_valid_book()
    {
        $book = $this->bookFactory()->first();

        $this
        ->delete("/books/{$book->id}")
        ->seeStatusCode(204)
        ->isEmpty();

        $this->notSeeInDatabase('books', ['id' => $book->id]);
    }
}
