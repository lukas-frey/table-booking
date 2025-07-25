<?php

use App\Models\Reservation;
use App\Models\User;
use App\Notifications\ReservationCancellationConfirmation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

it('can cancel own reservation', function () {
    Carbon::setTestNow('2025-07-21 12:00:00');

    Notification::fake();

    $user = User::factory()->create();
    $reservation = Reservation::factory()->recycle($user)->create();
    $this->actingAs($user);

    $this->post(route('reservations.cancel', [
        'reservation' => $reservation,
    ]))
        ->assertRedirectBack()
    ;

    $reservation->refresh();
    expect($reservation->cancelled_at->toDateTimeString())->toBe('2025-07-21 12:00:00');

    Notification::assertSentTo(
        [$user], ReservationCancellationConfirmation::class
    );
});

it('can not cancel reservation when not logged in', function () {
    $reservation = Reservation::factory()->create();

    Notification::fake();

    $this->post(route('reservations.cancel', [
        'reservation' => $reservation,
    ]))
        ->assertRedirect(route('login'))
    ;

    $reservation->refresh();
    expect($reservation->cancelled_at)->toBeNull();

    Notification::assertNothingSent();
});

it('can not cancel someone else\'s reservation', function () {
    $this->actingAs(User::factory()->create());
    $reservation = Reservation::factory()->create();

    Notification::fake();

    $this->post(route('reservations.cancel', [
        'reservation' => $reservation,
    ]))
        ->assertForbidden()
    ;

    $reservation->refresh();
    expect($reservation->cancelled_at)->toBeNull();

    Notification::assertNothingSent();
});
