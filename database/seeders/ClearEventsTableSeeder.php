<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClearEventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // // Disable foreign key checks
    // DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // // Truncate the events table
    // DB::table('events')->truncate();

    // // Re-enable foreign key checks
    // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
