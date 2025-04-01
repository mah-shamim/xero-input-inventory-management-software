<?php


use Database\Seeders\InitialSeeder;
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
        activity()->disableLogging();
        $this->call(InitialSeeder::class);
        activity()->enableLogging();
    }
}
