<?php

use App\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $zones=[
            ['name'=>'East'],
            ['name'=>'West'],
            ['name'=>'North'],
            ['name'=>'South'],
            ];
        foreach ($zones as $key => $value) {
           Zone::Create($value);
        }
    }
}
