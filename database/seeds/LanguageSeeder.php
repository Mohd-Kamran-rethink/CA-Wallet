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
            ['name'=>'Hindi'],
            ['name'=>'English'],
            ['name'=>'Bengali'],
            ['name'=>'Telugu'],
            ['name'=>'Marathi'],
            ['name'=>'Tamil'],
            ['name'=>'Urdu'],
            ['name'=>'Gujarati'],
            ['name'=>'Kannada'],
            ['name'=>'Odia'],
            ['name'=>'Malayalam'],
        ];
        foreach ($languages as $key => $value) {
            Language::create($value);
        }
    }
}
