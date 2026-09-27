<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'barber_id' => User::factory()->barber(),
            'service_id' => Service::factory(),
            'date' => fake()->dateTimeBetween('today', '+7 days')->format('Y-m-d'),
            'time' => fake()->randomElement(Appointment::timeSlots()),
            'status' => 'scheduled',
        ];
    }
}
