<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /**
     * The attributes that are mass assignable
     *
     * @var array
     **/
    protected $fillable = ['value'];

    public function rateable()
    {
        return $this->morphTo();
    }
}
