<?php

use App\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages=[
            ['name'=>'Primary'],
            ['name'=>'Bengali'],
            ['name'=>'Telugu'],
            ['name'=>'Marathi'],
            ['name'=>'Tamil'],
            ['name'=>'Urdu'],
            ['name'=>'Gujarati'],
            ['name'=>'Kannada'],
            ['name'=>'Odia'],
            ['name'=>'Malayalam'],
            ['name'=>'Multaniple'],
        ];
        foreach ($languages as $key => $value) {
            Language::create($value);
        }
    }
}
