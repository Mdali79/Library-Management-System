<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $roles = ['Student', 'Teacher', 'Librarian', 'Admin'];
        $batches = ['2021', '2022', '2023', '2024', '2025'];
        
        return [
            'name' => $this->faker->name,
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'contact' => $this->faker->phoneNumber,
            'role' => $this->faker->randomElement($roles),
            'department' => 'Computer Science',
            'batch' => $this->faker->randomElement($batches),
            'roll' => 'CS' . $this->faker->numberBetween(1000, 9999),
            'reg_no' => 'CS-' . $this->faker->unique()->numberBetween(10000, 99999),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'is_verified' => true,
        ];
    }


}
