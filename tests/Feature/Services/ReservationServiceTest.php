<?php

use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Once;

beforeEach(function () {
    config([
        'app.max_tables' => 2,
        'app.reservation_duration' => 2,
        'app.reservation_max_days' => 7,

        'app.schedule' => [
            'monday' => '12:00-20:00',
            'tuesday' => '12:00-20:00',
            'wednesday' => '12:00-20:00',
            'thursday' => '12:00-20:00',
            'friday' => '12:00-16:00',
            'saturday' => null,
            'sunday' => null,
        ],
    ]);

    // Freeze time (monday)
    Carbon::setTestNow(Carbon::parse('2025-07-21'));
});

it('has no unavailable dates when open daily and no reservations made', function () {
    config([
        'app.schedule' => [
            'monday' => '12:00-20:00',
            'tuesday' => '12:00-20:00',
            'wednesday' => '12:00-20:00',
            'thursday' => '12:00-20:00',
            'friday' => '12:00-20:00',
            'saturday' => '12:00-20:00',
            'sunday' => '12:00-20:00',
        ],
    ]);

    $service = app(ReservationService::class);

    $unavailableDates = $service->getUnavailableDates();

    expect($unavailableDates)->toBeEmpty();
});

it('has unavailable dates when not open daily', function () {
    $service = app(ReservationService::class);

    $unavailableDates = $service->getUnavailableDates();

    expect($unavailableDates)->not->toBeEmpty();
    expect($unavailableDates)->toHaveCount(2);
    expect($unavailableDates)->toContain(
        '2025-07-26',
        '2025-07-27',
    );
});

it('has unavailable dates when open daily and has fully booked dates', function () {
    $service = app(ReservationService::class);

    $fullyBookedDate = CarbonImmutable::parse('2025-07-24');
    $service->getTimeSlotsForDate($fullyBookedDate, '2 hours')
        ->each(
            fn ($timeSlot) => Reservation::factory()
                ->count(config('app.max_tables'))
                ->create([
                    'starts_at' => $fullyBookedDate->setTimeFromTimeString($timeSlot),
                    'ends_at' => $fullyBookedDate->setTimeFromTimeString($timeSlot)->addHours(config('app.reservation_duration')),
                ])
        )
    ;

    $unavailableDates = $service->getUnavailableDates();

    expect($unavailableDates)->not->toBeEmpty();
    expect($unavailableDates)->toHaveCount(3);
    expect($unavailableDates)->toContain(
        '2025-07-24',
        '2025-07-26',
        '2025-07-27',
    );
});

it('can get time slots for dates', function () {
    $service = app(ReservationService::class);

    expect($service->getTimeSlotsForDate(Carbon::parse('2025-07-25')))->not->toBeEmpty();
    expect($service->getTimeSlotsForDate(Carbon::parse('2025-07-25')))->toContain(
        '12:00',
        '12:30',
        '13:00',
        '13:30',
        '14:00',
    );

    expect($service->getTimeSlotsForDate(Carbon::parse('2025-07-26')))->toBeEmpty();
});

it('correctly calculates available time slots for date', function () {
    config([
        'app.schedule' => [
            'monday' => null,
            'tuesday' => null,
            'wednesday' => null,
            'thursday' => '12:15-16:45',
            'friday' => '12:15-12:30',
            'saturday' => '12:00-17:00',
            'sunday' => '12:00-17:00',
        ],
    ]);

    $service = app(ReservationService::class);

    $wednesday = CarbonImmutable::parse('2025-07-23');
    $thursday = CarbonImmutable::parse('2025-07-24');
    $friday = CarbonImmutable::parse('2025-07-25');
    $saturday = CarbonImmutable::parse('2025-07-26');
    $sunday = CarbonImmutable::parse('2025-07-27');

    // Thursday
    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => $thursday->setTimeFromTimeString('12:15'),
        'ends_at' => $thursday->setTimeFromTimeString('14:15'),
    ]);
    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => $thursday->setTimeFromTimeString('14:45'),
        'ends_at' => $thursday->setTimeFromTimeString('16:45'),
    ]);

    // Saturday
    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => $saturday->setTimeFromTimeString('12:30'),
        'ends_at' => $saturday->setTimeFromTimeString('14:30'),
    ]);

    // Sunday
    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => $sunday->setTimeFromTimeString('13:30'),
        'ends_at' => $sunday->setTimeFromTimeString('15:30'),
    ]);

    $availableTimeSlotsWednesday = $service->getAvailableTimeSlotsForDate($wednesday);
    $availableTimeSlotsThursday = $service->getAvailableTimeSlotsForDate($thursday);
    $availableTimeSlotsFriday = $service->getAvailableTimeSlotsForDate($friday);
    $availableTimeSlotsSaturday = $service->getAvailableTimeSlotsForDate($saturday);
    $availableTimeSlotsSunday = $service->getAvailableTimeSlotsForDate($sunday);

    expect($availableTimeSlotsWednesday)->toBeEmpty();
    expect($availableTimeSlotsThursday)->toBeEmpty();
    expect($availableTimeSlotsFriday)->toBeEmpty();
    expect($availableTimeSlotsSaturday)->not->toBeEmpty();
    expect($availableTimeSlotsSunday)->toBeEmpty();
});

it('correctly determines time slot availability on time range edge', function () {
    $service = app(ReservationService::class);

    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => today()->setTimeFromTimeString('12:00'),
        'ends_at' => today()->setTimeFromTimeString('14:00'),
    ]);

    Reservation::factory()->count(config('app.max_tables') - 1)->create([
        'starts_at' => today()->setTimeFromTimeString('14:00'),
        'ends_at' => today()->setTimeFromTimeString('16:00'),
    ]);
    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => today()->setTimeFromTimeString('16:00'),
        'ends_at' => today()->setTimeFromTimeString('18:00'),
    ]);
    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => today()->setTimeFromTimeString('18:00'),
        'ends_at' => today()->setTimeFromTimeString('20:00'),
    ]);

    $availableTimeSlots = $service->getAvailableTimeSlotsForDate(today());
    expect($availableTimeSlots)->not->toBeEmpty();
    expect($availableTimeSlots)->toContain('14:00');

    Once::flush();

    Reservation::factory()->create([
        'starts_at' => today()->setTimeFromTimeString('14:00'),
        'ends_at' => today()->setTimeFromTimeString('16:00'),
    ]);

    $availableTimeSlots = $service->getAvailableTimeSlotsForDate(today());
    expect($availableTimeSlots)->toBeEmpty();
});

it('correctly determines table available in free time range', function () {
    $service = app(ReservationService::class);

    $startsAt = CarbonImmutable::parse('2025-07-25 14:00');
    $endsAt = CarbonImmutable::parse('2025-07-25 16:00');

    // No reservations
    expect($service->isTableAvailable($startsAt, $endsAt))->toBeTrue();
});

it('correctly determines table un/available on time range edge', function () {
    $service = app(ReservationService::class);

    $startsAt = CarbonImmutable::parse('2025-07-24 14:00');
    $endsAt = CarbonImmutable::parse('2025-07-24 16:00');

    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => CarbonImmutable::parse('2025-07-24 12:00'),
        'ends_at' => CarbonImmutable::parse('2025-07-24 14:00'),
    ]);

    expect($service->isTableAvailable($startsAt, $endsAt))->toBeTrue();

    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => CarbonImmutable::parse('2025-07-24 16:00'),
        'ends_at' => CarbonImmutable::parse('2025-07-24 18:00'),
    ]);
    expect($service->isTableAvailable($startsAt, $endsAt))->toBeTrue();
});

it('correctly determines table unavailable on time range edge with slight overlap', function () {
    $service = app(ReservationService::class);

    $startsAt = CarbonImmutable::parse('2025-07-24 14:00');
    $endsAt = CarbonImmutable::parse('2025-07-24 16:00');

    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => CarbonImmutable::parse('2025-07-24 12:01'),
        'ends_at' => CarbonImmutable::parse('2025-07-24 14:01'),
    ]);

    expect($service->isTableAvailable($startsAt, $endsAt))->toBeFalse();

    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => CarbonImmutable::parse('2025-07-24 15:59'),
        'ends_at' => CarbonImmutable::parse('2025-07-24 17:59'),
    ]);

    expect($service->isTableAvailable($startsAt, $endsAt))->toBeFalse();
});

it('correctly frees up space when reservation is cancelled', function () {
    config([
        'app.schedule.monday' => '12:00-14:00',
    ]);

    $start = today()->setTimeFromTimeString('12:00');
    $end = today()->setTimeFromTimeString('14:00');

    Reservation::factory()->count(config('app.max_tables'))->create([
        'starts_at' => $start,
        'ends_at' => $end,
    ]);

    $service = app(ReservationService::class);

    $availableTimeSlots = $service->getAvailableTimeSlotsForDate(today());

    expect($availableTimeSlots)->toBeEmpty();
    expect($service->isTableAvailable($start, $end))->toBeFalse();

    Reservation::first()->update(['cancelled_at' => now()]);

    Once::flush();

    $availableTimeSlots = $service->getAvailableTimeSlotsForDate(today());

    expect($availableTimeSlots)->not->toBeEmpty();
    expect($availableTimeSlots)->toContain('12:00');
    expect($service->isTableAvailable($start, $end))->toBeTrue();
});
