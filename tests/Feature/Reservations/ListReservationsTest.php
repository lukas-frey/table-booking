<?php

use App\Livewire\Reservations\ListReservations;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Livewire\livewire;

it('can not render list reservations screen as unauthenticated', function () {
    $response = $this->get(route('reservations.index'));

    $response->assertRedirect(
        route('login')
    );
});

it('can not render list reservations screen as unverified', function () {
    $this->actingAs(User::factory()->unverified()->create());
    $response = $this->get(route('reservations.index'));

    $response->assertRedirect(
        route('verification.notice')
    );
});

it('can render list reservations screen as verified user', function () {
    $this->actingAs(User::factory()->create());
    $response = $this->get(route('reservations.index'));

    $response->assertSuccessful();
});

it('can see own reservations', function () {
    Carbon::setTestNow('2025-07-21 12:00');
    $user = User::factory()->create();
    $this->actingAs($user);

    // Own reservations
    Reservation::factory()
        ->recycle($user)
        ->createMany([
            ['starts_at' => '2025-07-21 12:00', 'ends_at' => '2025-07-21 14:00'],
            ['starts_at' => '2025-07-22 12:00', 'ends_at' => '2025-07-22 14:00'],
            ['starts_at' => '2025-07-22 13:00', 'ends_at' => '2025-07-22 15:00'],
        ])
    ;

    // Other reservations
    Reservation::factory()
        ->createMany([
            ['starts_at' => '2025-07-21 12:00', 'ends_at' => '2025-07-21 14:00'],
            ['starts_at' => '2025-07-21 14:00', 'ends_at' => '2025-07-22 16:00'],
            ['starts_at' => '2025-07-22 10:00', 'ends_at' => '2025-07-22 12:00'],
        ])
    ;

    livewire(ListReservations::class)
        ->assertSeeTextInOrder([
            '21. 7. 2025',
            '12:00 - 14:00',
            '22. 7. 2025',
            '12:00 - 14:00',
            '22. 7. 2025',
            '13:00 - 15:00',
        ])
    ;
});
