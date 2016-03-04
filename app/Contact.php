<?php

namespace Reset;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'user_id', 'json', 'href',
    ];

    public function user()
    {
        return $this->belongsTo('Reset\User');
    }
}
