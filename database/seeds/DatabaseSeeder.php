<?php

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DefaultManager::class,
            SourcesSeeder::class,
            LeadStatusOptionSeeder::class,
            StateSeeder::class,
            ZoneSeeder::class,
            LanguageSeeder::class,
        ]);
    }
}
