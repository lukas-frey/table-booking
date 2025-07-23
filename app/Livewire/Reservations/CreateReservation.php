<?php

namespace App\Livewire\Reservations;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class CreateReservation extends Component
{

    public int $guests = 2;

    public ?string $date = null;

    public ?string $time = null;

}
