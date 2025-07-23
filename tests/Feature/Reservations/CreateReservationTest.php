<?php

use App\Livewire\Reservations\CreateReservation;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Livewire\livewire;

beforeEach(function () {
    config([
        'app.max_tables' => 2,
        'app.reservation_duration' => 2,
        'app.reservation_max_days' => 7,

        'app.schedule' => [
            'monday' => '12:00-20:00',
            'tuesday' => '12:00-20:00',
            'wednesday' => '12:00-20:00',
            'thursday' => '12:00-20:00',
            'friday' => '12:00-16:00',
            'saturday' => null,
            'sunday' => null,
        ],
    ]);

    // Freeze time (monday)
    Carbon::setTestNow(Carbon::parse('2025-07-21'));
});

it('can not render create reservation screen as unauthenticated', function () {
    $response = $this->get(route('reservations.create'));

    $response->assertRedirect(
        route('login')
    );
});

it('can not render create reservation screen as unverified', function () {
    $this->actingAs(User::factory()->unverified()->create());
    $response = $this->get(route('reservations.create'));

    $response->assertRedirect(
        route('verification.notice')
    );
});

it('can render create reservation screen as verified user', function () {
    $this->actingAs(User::factory()->create());
    $response = $this->get(route('reservations.create'));

    $response->assertSuccessful();
});

it('can reserve a table', function () {
    $this->actingAs(User::factory()->create());

    livewire(CreateReservation::class)
        ->call('createReservation')
        ->assertHasErrors([
            'date',
            'time',
        ])
        ->fill([
            'date' => today(),
            'time' => '14:00',
        ])
        ->call('createReservation')
        ->assertHasNoErrors()
        ->assertRedirect(route('reservations.success', [
            'reservation' => Reservation::query()->first(),
        ]))
    ;
});

it('can not reserve a table for invalid date', function () {
    $this->actingAs(User::factory()->create());

    livewire(CreateReservation::class)
        ->fill([
            'date' => Carbon::parse('2025-07-26'),
            'time' => '14:00',
        ])
        ->call('createReservation')
        ->assertHasErrors()
    ;
});
