<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'subject', 'client_id', 'manager_id', 'status',
    ];

    /* RELATIONS */
    public function dialogue()
    {
        return $this->hasMany(RequestMessage::class, 'request_id', 'id');
    }
}
