<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Carbon\WeekDay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReservationService
{
    protected array $schedule;

    protected int $maxTables;

    protected int $reservationDuration;

    protected CarbonInterface $startDate;

    protected CarbonInterface $endDate;

    public function __construct()
    {
        $this->schedule = config('app.schedule');
        $this->maxTables = config('app.max_tables');
        $this->reservationDuration = config('app.reservation_duration');
        $this->startDate = Carbon::today();
        $this->endDate = Carbon::today()->addDays(config('app.reservation_max_days'));
    }

    /**
     * Get all unavailable dates for the reservable time range.
     */
    public function getUnavailableDates(): Collection
    {
        $result = collect();
        $period = CarbonPeriod::create($this->startDate, '1 day', $this->endDate);

        /** @var Carbon $date */
        foreach ($period as $date) {
            $schedule = $this->getScheduleForDate($date);

            // No schedule for this day or set to null = closed
            if ($schedule === null) {
                $result->push($date->toDateString());
            } elseif ($this->getAvailableTimeSlotsForDate($date)->isEmpty()) {
                $result->push($date->toDateString());
            }
        }

        return $result;
    }

    /**
     * Returns the restaurant's opening schedule for the given date.
     */
    public function getScheduleForDate(CarbonInterface $date): ?string
    {
        $weekDay = WeekDay::fromNumber($date->weekday());
        $weekDayName = strtolower($weekDay->name);

        return data_get($this->schedule, $weekDayName);
    }

    /**
     * Get all time slots for the specified date based on the restaurant's schedule.
     */
    public function getTimeSlotsForDate(CarbonInterface $date, string $period = '30 minutes'): Collection
    {
        if ($schedule = $this->getScheduleForDate($date)) {
            [$start, $end] = explode('-', $schedule);

            $start = Carbon::createFromTimeString($start)->ceilMinutes(30);
            $end = Carbon::createFromTimeString($end)->floorMinutes(30);

            // We move end forward by x hours to accommodate for the reservation duration
            $end->subHours($this->reservationDuration);

            // Start needs to be after end. Either the start / end range is misconfigured, or the range does not fit the reservation duration
            if ($start > $end) {
                return collect();
            }

            return collect(CarbonPeriod::create($start, $period, $end)
                ->toArray())
                ->map(fn (Carbon $date) => $date->toTimeString('minute'))
            ;
        }

        return collect();
    }

    /**
     * Get all available time slots for the specified date based on the restaurant's schedule.
     */
    public function getAvailableTimeSlotsForDate(CarbonInterface $date): Collection
    {
        $availableSlots = collect();
        $reservations = $this->getReservations();

        foreach ($this->getTimeSlotsForDate($date) as $slot) {
            $slotStart = $date->copy()->setTimeFromTimeString($slot);
            $slotEnd = $date->copy()->setTimeFromTimeString($slot)->addHours($this->reservationDuration);

            // Cannot select a time slot in the past
            if ($slotStart->isBefore(now())) {
                continue;
            }

            $overlappingReservations = $reservations
                ->where('starts_at', '<', $slotEnd)
                ->where('ends_at', '>', $slotStart)
                ->count()
            ;

            if ($overlappingReservations < $this->maxTables) {
                $availableSlots->push($slot);
            }
        }

        return $availableSlots;
    }

    /**
     * Checks if a table is still available within the specified time range.
     */
    public function isTableAvailable(CarbonInterface $startsAt, CarbonInterface $endsAt): bool
    {
        $availableTimeSlots = $this->getAvailableTimeSlotsForDate($startsAt);

        // No available time slot
        if (! $availableTimeSlots->contains($startsAt->toTimeString('minute'))) {
            return false;
        }

        // No free table
        return Reservation::query()
            ->whereNull('cancelled_at')
            ->where(
                static fn (Builder $query) => $query
                    ->where(
                        fn (Builder $query) => $query
                            ->where('starts_at', '<', $endsAt)
                            ->where('ends_at', '>', $startsAt)
                    )
                    ->orWhere(
                        fn (Builder $query) => $query
                            ->where('ends_at', '>', $startsAt)
                            ->where('starts_at', '<', $endsAt)
                    )
            )
            ->count() < $this->maxTables
        ;
    }

    /**
     * Get the reservations for the reservable date range.
     *
     * Will be cached for one request lifecycle.
     */
    protected function getReservations(): Collection
    {
        return once(
            fn () => Reservation::query()
                ->whereNull('cancelled_at')
                ->whereBetween('starts_at', [$this->startDate, $this->endDate])
                ->get()
        );
    }
}
