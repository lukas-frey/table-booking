<?php

use App\Livewire\Auth\ConfirmPassword;
use App\Models\User;
use Livewire\Livewire;

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('password.confirm'));

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(ConfirmPassword::class)
        ->set('password', 'password')
        ->call('confirmPassword');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('reservations.index', absolute: false));
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(ConfirmPassword::class)
        ->set('password', 'wrong-password')
        ->call('confirmPassword');

    $response->assertHasErrors(['password']);
});
