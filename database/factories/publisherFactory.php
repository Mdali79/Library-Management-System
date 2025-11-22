<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class publisherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Computer Science book publishers
        $csPublishers = [
            'MIT Press',
            'O\'Reilly Media',
            'Addison-Wesley',
            'Pearson Education',
            'McGraw-Hill',
            'Prentice Hall',
            'Wiley',
            'Packt Publishing',
            'Manning Publications',
            'Apress',
            'No Starch Press',
            'CRC Press',
            'Springer',
            'Cambridge University Press',
            'Oxford University Press',
        ];
        
        return [
            'name' => $this->faker->randomElement($csPublishers)
        ];
    }
}
