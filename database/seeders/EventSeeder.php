<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            'Pahang', 'Negeri Sembilan', 'Sabah', 'Sarawak', 'Terengganu',
            'Kelantan', 'Perak', 'Johor', 'Melaka', 'Kedah'
        ];

        $statuses = ['draft', 'submitted', 'approved', 'completed'];

        foreach (range(1, 10) as $i) {
            $location = $locations[array_rand($locations)];
            $startDate = now()->setDate(2025, 8, 1)->addDays(rand(0, 120));
            $endDate = (clone $startDate)->addDays(rand(1, 5)); // 1-5 days after start

            Event::create([
                'title' => "Trip to {$location}",
                'description' => "Join us for an unforgettable trip to {$location}!",
                'location' => $location,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'max_participants' => rand(2, 10),
                'cost' => rand(50, 500),
                'status' => 'draft',
                'created_by' => 1,
            ]);
        }
    }
}
