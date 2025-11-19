<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lookup extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
