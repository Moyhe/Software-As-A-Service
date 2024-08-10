<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Geni',
            'email' => 'Geni@gmail.com',
            'password' => Hash::make('kenkanekiTouka123'),
            'email_verified_at' => Carbon::now()
        ]);

        Feature::create([

            'image' => 'https://static-00.iconduck.com/assets.00/plus-icon-2048x2048-z6v59bd6.png',
            'route_name' => 'feature1.index',
            'name' => 'calculate sum',
            'description' => 'calculate sum of two numbers',
            'required_credits' => 1,
            'active' => true
        ]);


        Feature::create([

            'image' => 'https://cdn-icons-png.freepik.com/512/929/929430.png',
            'route_name' => 'feature2.index',
            'name' => 'calculate differences',
            'description' => 'calculate difference of two numbers',
            'required_credits' => 3,
            'active' => true
        ]);


        Package::create([
            'name' => 'Basic',
            'price' => 5,
            'credits' => 20
        ]);

        Package::create([
            'name' => 'Silver',
            'price' => 20,
            'credits' => 100
        ]);

        Package::create([
            'name' => 'Gold',
            'price' => 50,
            'credits' => 500
        ]);
    }
}
