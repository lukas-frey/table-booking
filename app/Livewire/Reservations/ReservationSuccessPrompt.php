<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['withHeader' => false])]
class ReservationSuccessPrompt extends Component
{
    public Reservation $reservation;
}
