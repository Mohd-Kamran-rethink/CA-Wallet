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
        $sources = [
            ['name' => 'Test Manager', 'email' => 'manager@ca.com', 'phone' => '928277273', 'role' => 'manager', 'password' => '123456789'],
            ['name' => 'Test Manager', 'email' => 'customer@ca.com', 'phone' => '928277273', 'role' => 'customer_care_manager', 'password' => '123456789'],
        ];

        foreach ($sources as $item) {
            $manager = new User();
            $manager->name = $item['name'];
            $manager->email = $item['email'];
            $manager->phone = $item['phone'];
            $manager->role = $item['role'];
            $manager->is_admin = "Yes";
            $manager->password = Hash::make($item['password']);
            $manager->save();
        }
        
       
    }
}
       
        
