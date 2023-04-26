<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class Attendance extends Model
{

    protected $fillable = ['user_id', 'date', 'time', 'action'];
}
