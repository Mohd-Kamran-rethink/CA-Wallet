<?php

namespace App\Http\Controllers;

use App\BankDetail;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function listActiveBanks() {
        $banks=BankDetail::whereNull('customer_id')->where('is_active','=','yes')->get();
        return view('Admin.Baanks.list',compact('banks'));
    }
}
