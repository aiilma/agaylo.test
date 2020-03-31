<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestMessage extends Model
{
    protected $fillable = [
        'body', 'attachment', 'request_id', 'author_id', 'is_checked'
    ];

    /* RELATIONS */
    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public $timestamps = false;
}
