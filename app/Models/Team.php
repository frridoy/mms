<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'team_number', 'user_id', 'is_manager', 'is_active', 'created_by'];

    public function member()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allMembers()
    {
        return $this->hasMany(Team::class, 'team_number', 'team_number');
    }

    public function onlyMembers()
    {
        return $this->hasMany(Team::class, 'team_number', 'team_number')
               ->whereNot('is_manager', 1);
    }

    public function manager()
    {
        return $this->hasOne(Team::class, 'team_number', 'team_number')
            ->where('is_manager', 1);
    }
}
