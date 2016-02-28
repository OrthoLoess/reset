<?php

namespace Reset;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'user_id', 'type', 'entity_id', 'name', 'standing'
    ];
}
