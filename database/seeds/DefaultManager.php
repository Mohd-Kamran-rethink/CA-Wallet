<?php

use App\Manager;
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
        $Manager=new Manager();
        $Manager->name="Kamran Ali";
        $Manager->email="manager@gmail.com";
        $Manager->phone="928277273";
        $Manager->password=Hash::make("123456789");
        $Manager->save();
    }
}
       
        
