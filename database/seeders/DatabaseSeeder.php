<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Contract;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('contracts')->truncate();
        DB::table('users')->truncate();
        DB::table('divisions')->truncate();

        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            DivisionSeeder::class,
            UserSeeder::class,
            ContractSeeder::class,
        ]);
    }
}
