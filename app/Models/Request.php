<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'subject', 'client_id', 'manager_id', 'status',
    ];

    public function getStatus()
    {
        return $this->status === 0 ? 'Закрытая' : 'Открытая';
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
