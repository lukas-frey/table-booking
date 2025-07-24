<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ListReservations extends Component
{
    public Collection $reservationsGroupedByDate;

    public function mount(): void
    {
        $this->reservationsGroupedByDate = Reservation::query()
            ->with(['user'])
            ->whereFuture('starts_at')
            ->whereBelongsTo(auth()->user())
            ->orderBy('starts_at')
            ->get()
            ->toBase()
            ->groupBy(static fn (Reservation $reservation) => $reservation->starts_at->toDateString(), true)
        ;

    }

    public function render(): View
    {
        return view('livewire.reservations.list-reservations')
            ->layout('components.layouts.app', [
                'centered' => $this->reservationsGroupedByDate->isEmpty(),
            ])
        ;
    }
}
