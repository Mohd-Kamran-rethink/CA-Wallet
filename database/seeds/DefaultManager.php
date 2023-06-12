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
            ['name' => 'CA Super Manager', 'email' => 'supermanager@ca.com', 'phone' => '928277273', 'role' => 'super_manager', 'password' => '123456789'],
            ['name' => 'CA Normal Manager', 'email' => 'manager@ca.com', 'phone' => '928277273', 'role' => 'manager', 'password' => '123456789'],
            ['name' => 'CA Normal Manager', 'email' => 'customer@ca.com', 'phone' => '928277273', 'role' => 'customer_care_manager', 'password' => '123456789'],
            ['name' => 'Expense Normal Manager', 'email' => 'expense@ca.com', 'phone' => '928277273', 'role' => 'expense_manager', 'password' => '123456789'],
        ];

        foreach ($sources as $item) {
            $manager = new User();
            $manager->name = $item['name'];
            $manager->email = $item['email'];
            $manager->phone = $item['phone'];
            $manager->role = $item['role'];
            $manager->is_admin = "No";
            $manager->password = Hash::make($item['password']);
            $manager->save();
        }
        
       
    }
}
       
        
