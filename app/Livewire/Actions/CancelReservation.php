<?php

namespace App\Livewire\Actions;

use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CancelReservation
{
    public function __invoke(Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->update([
            'cancelled_at' => now(),
        ]);

        return back();
    }
}
