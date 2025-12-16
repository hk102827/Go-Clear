<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'Waste Collection',
                'slug' => 'waste-collection',
                'description' => 'Regular waste collection service',
                'is_active' => true,
            ],
            [
                'name' => 'Skip Hire',
                'slug' => 'skip-hire',
                'description' => 'Skip bin hire service',
                'is_active' => true,
            ],
            [
                'name' => 'Home Removals',
                'slug' => 'home-removals',
                'description' => 'Home and office removal service',
                'is_active' => true,
            ],
            [
                'name' => 'Muck Away',
                'slug' => 'muck-away',
                'description' => 'Muck and debris removal',
                'is_active' => true,
            ],
            [
                'name' => 'Cement / Aggregates',
                'slug' => 'cement-aggregates',
                'description' => 'Cement and aggregates delivery',
                'is_active' => true,
            ],
            [
                'name' => 'Handyman',
                'slug' => 'handyman',
                'description' => 'General handyman services',
                'is_active' => true,
            ],
            [
                'name' => 'Drainage',
                'slug' => 'drainage',
                'description' => 'Drainage services',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}