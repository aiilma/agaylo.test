<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestMessage extends Model
{
    protected $fillable = [
        'body', 'attachment', 'request_id',
    ];

    /* RELATIONS */
    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id', 'id');
    }
}
