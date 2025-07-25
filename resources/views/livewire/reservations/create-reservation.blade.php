<div class="flex flex-col gap-6">
    <x-heading :title="__('Make a reservation')"
               :description="__('Please fill out the form below to reserve a table')"
    />

    <x-slot:aside class=" bg-contain md:h-[calc(100dvh-8.5rem)] md:sticky top-24 mt-0 mb-auto rounded-xl overflow-hidden">
        <div
            class="m-auto flex flex-col w-2/3 md:w-full aspect-square items-center from-transparent via-white/70 to-white dark:opacity-70 dark:from-white/20 dark:via-neutral-900/70 dark:to-neutral-950 [background:_radial-gradient(circle,var(--tw-gradient-from)_0%,var(--tw-gradient-via)_40%,var(--tw-gradient-to)_65%),url(../images/floor.png)] [background-size:_100%]! rounded-full"
        >
            <div id="animation-target" class="flex flex-col m-auto w-3/5 gap-6"></div>
        </div>
    </x-slot:aside>

    @teleport('#animation-target')
    <div
        x-data="seatingAnimation({
            chairCount: 2,
            maxChairsCount: @js(config('app.seats_per_table')),
            chairImage: @js(Vite::asset('resources/images/chair.png')),
            plateImages: @js([
                Vite::asset('resources/images/plate1.png'),
                Vite::asset('resources/images/plate2.png'),
                Vite::asset('resources/images/plate3.png'),
            ])
        })"
        wire:ignore
        class="relative w-full aspect-square h-auto flex items-center justify-center"
    >
        <!-- Table -->
        <img
            src="{{Vite::asset('resources/images/table.png')}}"
            class="table absolute w-full h-full z-5 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
            alt="Table"
        >
    </div>
    @endteleport

    <form wire:submit="createReservation" class="flex flex-col gap-6">
        <!-- Guests count -->
        <flux:input
            wire:model.debounce="guests"
            :label="__('Amount of guests')"
            type="number"
            autofocus
            min="1"
            max="{{config('app.seats_per_table')}}"
            required
        />

        <flux:field>
            <flux:label>{{__('Select a date')}}</flux:label>
            <div x-data="datepicker({
                state:  $wire.entangle('date').live,
                minDate: @js(today()),
                maxDate: @js(today()->addDays(config('app.reservation_max_days'))),
                disabledDates: @js($this->getDisabledDates())
            })"
                 class="w-full"
                 wire:ignore
            ></div>
            <flux:error name="date"></flux:error>
        </flux:field>

        <!-- Time -->
        <flux:radio.group wire:model="time"
                          :label="__('Select a time')"
                          class="radio-buttons w-full grid grid-cols-4 gap-1.5"
        >
            @forelse ($this->getAvailableTimeSlotsForSelectedDate() as $timeSlot)
                <flux:radio value="{{$timeSlot}}" :label="$timeSlot" class="peer"/>
            @empty
                <flux:callout class="col-span-full">
                    <flux:callout.text>{{__('No time slots available')}}</flux:callout.text>
                </flux:callout>
            @endforelse
        </flux:radio.group>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Reserve table now') }}</flux:button>
        </div>
    </form>
</div>
