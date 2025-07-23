<?php

use App\Models\User;

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
