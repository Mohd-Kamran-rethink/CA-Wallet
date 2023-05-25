<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'source_id', 'date', 'name', 'number', 'language', 'idName', 'agent_id','manager_id','is_approved','leads_date'
    ];
    
}
