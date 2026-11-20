<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void {
    User::forceCreate([
        'name' => 'Admin', 'email' => 'admin@tiket.test',
        'password' => 'password', 'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    foreach ([['Budi', 'budi@tiket.test'], ['Sari', 'sari@tiket.test']] as [$n, $e]) {
        User::forceCreate([
            'name' => $n, 'email' => $e,
            'password' => 'password', 'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }

    $this->call(RouteScheduleSeeder::class);
    }  
}
