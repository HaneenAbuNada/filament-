<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name'];

public function states() {
    return $this->hasMany(State::class);
}
}
