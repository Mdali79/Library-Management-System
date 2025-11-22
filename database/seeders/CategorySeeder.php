<?php

namespace Database\Seeders;

use App\Models\category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Programming Languages',
            'Data Structures & Algorithms',
            'Database Systems',
            'Software Engineering',
            'Computer Networks',
            'Operating Systems',
            'Web Development',
            'Mobile Development',
            'Artificial Intelligence',
            'Machine Learning',
            'Cybersecurity',
            'Cloud Computing',
            'Computer Architecture',
            'Software Testing',
            'Project Management',
        ];

        foreach ($categories as $categoryName) {
            category::firstOrCreate(['name' => $categoryName]);
        }
    }
}
