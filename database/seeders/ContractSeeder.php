<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Contract;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $contractCount = rand(1, 3);
            $startDate = Carbon::now()->subYears(2)->startOfMonth();

            for ($i = 0; $i < $contractCount; $i++) {
                $endDate = (clone $startDate)->addMonths(6);

                $contract = Contract::create([
                    'user_id' => $user->id,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'agreement_type' => fake()->randomElement(['PKWT', 'PKWTT', 'Internship']),
                    'attachment' => null,
                ]);

                if ($i === 0 && $user->join_date === null) {
                    $user->join_date = $startDate->toDateString();
                    $user->save();
                }

                $startDate = (clone $endDate)->addDays(1); 
            }
        }
    }
}
