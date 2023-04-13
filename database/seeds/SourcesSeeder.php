<?php

use App\Source;
use Illuminate\Database\Seeder;

class SourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources=[
            ['name'=>'Club Data'],
            ['name'=>'Other Sources Data'],
            ['name'=>'Cricadda Data'],
            ['name'=>'Social Media (Manish)'],
            ['name'=>'Wati'],
            ['name'=>'CRM'],
            ['name'=>'Refrence'],
            ['name'=>'Social Media (Chetan)'],
            ['name'=>'Retention Call'],
        ];
        foreach ($sources as $key => $value) {
            Source::create($value);
        }
    }
}
