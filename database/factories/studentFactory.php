<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class studentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender=['male','female'];
        $batches = ['2021', '2022', '2023', '2024', '2025'];
        $roles = ['Student', 'Teacher', 'Librarian'];
        
        return [
            'name' => $this->faker->name,
            'age' => random_int(18,80),
            'gender' => $gender[random_int(0,1)],
            'email' => $this->faker->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'class' => 'CS-' . $this->faker->randomElement(['101', '201', '301', '401', '501']),
            'role' => $this->faker->randomElement($roles),
            'department' => 'Computer Science',
            'batch' => $this->faker->randomElement($batches),
            'roll' => 'CS' . $this->faker->numberBetween(1000, 9999),
            'reg_no' => 'CS-' . $this->faker->unique()->numberBetween(10000, 99999),
            'borrowing_limit' => function (array $attributes) {
                if ($attributes['role'] == 'Teacher') return 10;
                if ($attributes['role'] == 'Librarian') return 15;
                return 5;
            },
        ];
    }
}
