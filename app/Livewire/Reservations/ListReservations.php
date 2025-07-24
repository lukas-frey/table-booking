<?php

namespace App\Livewire\Reservations;

use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use function Livewire\Volt\layout;

//#[Layout('components.layouts.app', ['centered' => true])]
class ListReservations extends Component
{
    public int $guests = 2;

    public ?Carbon $date = null;

    public ?string $time = null;

    protected function rules(): array
    {
        return [
            'guests' => [
                'required',
                'int',
                'min:1',
                'max:' . config('app.seats_per_table'),
            ],
            'date' => [
                'required',
                'date',
                Rule::date()->todayOrAfter(),
                Rule::date()->beforeOrEqual(today()->addDays(config('app.reservation_max_days'))),
            ],
            'time' => 'required|string|max:5',
        ];
    }

    public function updatingDate(?Carbon &$value): void
    {
        $value = $value?->setTimezone('Europe/Prague');
    }

    public function createReservation(): void
    {
        $data = $this->validate();

        $guests = data_get($data, 'guests');
        $startsAt = CarbonImmutable::parse(data_get($data, 'date'))->setTimeFromTimeString(data_get($data, 'time'));
        $endsAt = $startsAt->addHours(config('app.reservation_duration'));

        // Check if the reservation is still valid
        if (!app(ReservationService::class)->isTableAvailable($startsAt, $endsAt)) {
            $this->addError('time', __('Your selected date and time is not available.'));

            return;
        }

        if (
            $reservation = Reservation::query()
                ->create([
                    'guests' => $guests,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'user_id' => auth()->id(),
                ])
        ) {
            $this->redirect(route('reservations.success', [
                'reservation' => $reservation,
            ]));
        }
    }

    protected function getDisabledDates(): Collection
    {
        return app(ReservationService::class)->getUnavailableDates();
    }

    protected function getAvailableTimeSlotsForSelectedDate(): Collection
    {
        return $this->date
            ? app(ReservationService::class)
                ->getAvailableTimeSlotsForDate(Carbon::parse($this->date))
            : collect();
    }

    public function render(): View
    {
        return view('livewire.reservations.list-reservations')
            ->layout('components.layouts.app', [
                'centered' => true || Reservation::query()->get()->isEmpty()
            ]);
    }
}
