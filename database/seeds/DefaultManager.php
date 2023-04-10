<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultManager extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Manager=new User();
        $Manager->name="Kamran Ali";
        $Manager->email="manager@gmail.com";
        $Manager->phone="928277273";
        $Manager->role="manager";
        $Manager->password=Hash::make("123456789");
        $Manager->save();
    }
}
       
        
