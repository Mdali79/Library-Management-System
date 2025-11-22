<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class autherFactory  extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Famous Computer Science authors
        $csAuthors = [
            'Thomas H. Cormen',
            'Robert C. Martin',
            'Gang of Four',
            'Andrew Hunt',
            'David Thomas',
            'Abraham Silberschatz',
            'James Kurose',
            'Keith Ross',
            'Harold Abelson',
            'Stuart Russell',
            'Peter Norvig',
            'Ian Goodfellow',
            'Ethem Alpaydin',
            'Ryan Dahl',
            'Mark Zuckerberg',
            'Tim Berners-Lee',
            'Linus Torvalds',
            'Guido van Rossum',
            'Bjarne Stroustrup',
            'James Gosling',
        ];
        
        return [
            'name' => $this->faker->randomElement($csAuthors)
        ];
    }
}
