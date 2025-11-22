<?php

namespace Database\Seeders;

use App\Models\settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (settings::count() == 0) {
            settings::create([
                'return_days' => 14,
                'fine_per_day' => 5.00,
                'fine_grace_period_days' => 14,
                'max_borrowing_limit_student' => 5,
                'max_borrowing_limit_teacher' => 10,
                'max_borrowing_limit_librarian' => 15,
            ]);
        }
    }
}
