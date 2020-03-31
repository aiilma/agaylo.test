<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'subject', 'client_id', 'manager_id', 'status',
    ];

    public function getNewMessages($id)
    {
        return $this->dialogue()->where('is_checked', 0)->where('author_id', '!=', $id);
    }

    /* RELATIONS */
    public function dialogue()
    {
        return $this->hasMany(RequestMessage::class, 'request_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }
}
