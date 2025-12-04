<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
  use HasFactory;

class PlaceVote extends Model
{

    protected $fillable = ['place_id', 'user_id'];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

