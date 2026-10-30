<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(CountriesTableSeeder::class);
        // $this->call(StatesTableSeeder::class);
        // $this->call(CitiesTableChunkOneSeeder::class);
        // $this->call(CitiesTableChunkTwoSeeder::class);
        // $this->call(CitiesTableChunkThreeSeeder::class);
        $this->call(CitiesTableChunkFourSeeder::class);
        $this->call(CitiesTableChunkFiveSeeder::class);
        $this->call(CitiesChunkSixSeeder::class);
        $this->call(CitiesTableChunkSevenSeeder::class);

        // if (DB::connection()->getName() == 'pgsql') {
            // $tablesToCheck = ['countries', 'states', 'cities'];
            // foreach ($tablesToCheck as $tableToCheck) {
            //     // $this->command->info('Checking the next id sequence for ' . $tableToCheck);
            //     $highestId = DB::table($tableToCheck)->select(DB::raw('MAX(id)'))->first();
            //     $nextId = DB::table($tableToCheck)->select(DB::raw('nextval(\''.$tableToCheck.'_id_seq\')'))->first();
            //     if ($nextId->nextval < $highestId->max) {
            //         DB::select('SELECT setval(\''.$tableToCheck.'_id_seq\', '.$highestId->max.')');
            //         $highestId = DB::table($tableToCheck)->select(DB::raw('MAX(id)'))->first();
            //         $nextId = DB::table($tableToCheck)->select(DB::raw('nextval(\''.$tableToCheck.'_id_seq\')'))->first();
            //         if ($nextId->nextval > $highestId->max) {
            //             // $this->command->info($tableToCheck . ' autoincrement corrected');
            //         } else {
            //             // $this->command->info('Arff! The nextval sequence is still all screwed up on ' . $tableToCheck);
            //         }
            //     }
            // }

        // }
    }
}
