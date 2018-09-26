<?php

namespace App;

use Log;

/**
* Trait to enable polymorphic ratings on a model. 7*
* @package App
*/
trait Rateable
{
    public function ratings()
    {
        Log::info("ratings");
        return $this->morphMany(Rating::class, 'rateable');
    }
}
