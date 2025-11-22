<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\category;
use App\Models\auther;
use App\Models\publisher;

class bookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories = category::pluck('id')->toArray();
        $authors = auther::pluck('id')->toArray();
        $publishers = publisher::pluck('id')->toArray();
        
        // Computer Science related book titles
        $csBookTitles = [
            'Introduction to Algorithms',
            'Clean Code: A Handbook of Agile Software Craftsmanship',
            'Design Patterns: Elements of Reusable Object-Oriented Software',
            'The Pragmatic Programmer',
            'Database System Concepts',
            'Computer Networks: A Top-Down Approach',
            'Operating System Concepts',
            'Structure and Interpretation of Computer Programs',
            'Artificial Intelligence: A Modern Approach',
            'Deep Learning',
            'Introduction to Machine Learning',
            'Web Development with Node.js',
            'Mobile App Development with React Native',
            'Cloud Computing: Concepts, Technology & Architecture',
            'Cybersecurity Essentials',
            'Software Engineering: A Practitioner\'s Approach',
            'Data Structures and Algorithms in Python',
            'Java: The Complete Reference',
            'Python Programming: An Introduction to Computer Science',
            'C++ Programming: From Problem Analysis to Program Design',
        ];
        
        return [
            'name' => $this->faker->randomElement($csBookTitles),
            'category_id' => !empty($categories) ? $this->faker->randomElement($categories) : 1,
            'auther_id' => !empty($authors) ? $this->faker->randomElement($authors) : 1,
            'publisher_id' => !empty($publishers) ? $this->faker->randomElement($publishers) : 1,
            'status' => 'Y',
            'isbn' => $this->faker->unique()->isbn13,
            'edition' => $this->faker->randomElement(['1st', '2nd', '3rd', '4th', '5th', '6th']),
            'publication_year' => $this->faker->numberBetween(2015, 2024),
            'description' => $this->faker->paragraph(3),
            'total_quantity' => $this->faker->numberBetween(1, 10),
            'available_quantity' => function (array $attributes) {
                return $attributes['total_quantity'] ?? $this->faker->numberBetween(1, 5);
            },
            'issued_quantity' => 0,
        ];
    }
}
