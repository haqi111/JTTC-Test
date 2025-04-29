<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            ['division_code' => '01', 'name' => 'Human Capital'],
            ['division_code' => '02', 'name' => 'Internal Audit'],
            ['division_code' => '03', 'name' => 'Outlet Operations'],
            ['division_code' => '04', 'name' => 'Finance & Accounting'],
            ['division_code' => '05', 'name' => 'Marketing & Sales'],
            ['division_code' => '06', 'name' => 'Logistics'],
            ['division_code' => '07', 'name' => 'Production'],
            ['division_code' => '08', 'name' => 'Bussines Intelligence'],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}
