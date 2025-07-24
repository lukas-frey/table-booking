<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = $this->faker->dateTimeBetween('-14 days', '+ 14 days');
        $endsAt = Carbon::parse($startsAt)->addHours(config('app.reservation_duration'));

        return [
            'guests' => $this->faker->numberBetween(1, config('app.seats_per_table')),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'cancelled_at' => null,
            'user_id' => User::factory(),
        ];
    }
}
