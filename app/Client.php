<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    
    public function depositHistories()
{
    return $this->hasManyThrough(DepositHistory::class, Deposit::class, 'client_id', 'deposit_id', 'id', 'id');
}
}
