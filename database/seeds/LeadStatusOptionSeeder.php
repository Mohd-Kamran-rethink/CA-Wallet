<?php

use App\LeadStatusOption;
use Illuminate\Database\Seeder;

class LeadStatusOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources=[
            ['name'=>'Deposited'],
            ['name'=>'Inserted'],
            ['name'=>'Not Answered'],
            ['name'=>'Not Reachable'],
            ['name'=>'Transfered'],
            ['name'=>'Demo Id'],
            ['name'=>'Id created'],
            ['name'=>'Call back'],
            ['name'=>'Existing Customer'],
            ['name'=>'Wrong Number'],
            ['name'=>'Busy'],
            ['name'=>'Not Intrested'],
            ['name'=>'Future Events'],
            ['name'=>'Pending'],
            ['name'=>'Happy'],
            ['name'=>'Not Happy'],
        ];
        foreach ($sources as $key => $value) {
            LeadStatusOption::create($value);
        }
    }
}
