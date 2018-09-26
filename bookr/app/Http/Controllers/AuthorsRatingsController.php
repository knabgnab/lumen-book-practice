<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;
use App\Transformer\RatingTransformer;
use Log;

/**
 * Manage an Author's Ratings
*/
class AuthorsRatingsController extends Controller
{
    public function store(Request $request, $authorId)
    {
        $author = Author::findOrFail($authorId);
        $rating = $author->ratings()->create(['value' => $request->get('value')]);
        $data = $this->item($rating, new RatingTransformer());

        return response()->json($data, 201);
    }

    /**
     * @param $authorId
    * @param $ratingId
    * @return \Laravel\Lumen\Http\ResponseFactory
    */
    public function destroy($authorId, $ratingId)
    {
        Log::info("destroy ".$authorId." ".$ratingId);
        /** @var \App\Author $author */
        $author = Author::findOrFail($authorId);
        Log::info(print_r("f ".$author->ratings()->findOrFail($ratingId), true));
        $author
            ->ratings()
            ->findOrFail($ratingId)
            ->delete();

        return response(null, 204);
    }
}
