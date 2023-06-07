<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DuplicateLead extends Model
{
    protected $fillable = [
        'source_id', 'date', 'name', 'number', 'language', 'state' ,'zone', 'idName', 'agent_id','manager_id','is_approved'
    ];
}
