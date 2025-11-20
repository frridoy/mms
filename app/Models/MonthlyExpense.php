<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyExpense extends Model
{
    protected $fillable = [
        'lookup_id',
        'month',
        'year',
        'amount',
        'created_by',
        'team_id',
        'description',
    ];

    public function lookup()
    {
        return $this->belongsTo(Lookup::class, 'lookup_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
