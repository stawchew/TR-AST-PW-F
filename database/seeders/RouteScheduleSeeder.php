<?php
namespace Database\Seeders;

use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class RouteScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Salatiga', 'Semarang', 60,  50000],
            ['Salatiga', 'Yogyakarta', 150, 85000],
            ['Semarang', 'Solo', 120, 75000],
        ];

        foreach ($data as [$o, $d, $dur, $price]) {
            $route = Route::create(['origin' => $o, 'destination' => $d, 'duration_minutes' => $dur]);

            foreach ([1, 2, 3] as $day) {
                Schedule::create([
                    'route_id' => $route->id,
                    'departure_at' => now()->addDays($day)->setTime(8, 0),
                    'price' => $price,
                    'total_seats' => 10,
                    'available_seats' => 10,
                ]);
            }
        }
    }
}
