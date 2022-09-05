<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'forms';

    protected $fillable = [
        'img', 'user_id'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
