<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Make a reservation')"
                   :description="__('Please fill out the form below to reserve a table')"
    />

    <!-- Session Status -->
    {{--    <x-auth-session-status class="text-center" :status="session('status')" />--}}

    <form wire:submit="createReservation" class="flex flex-col gap-6">
        <!-- Guests count -->
        <flux:input
            wire:model="guests"
            :label="__('Amount of guests')"
            type="number"
            autofocus
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
                          class="w-full grid grid-cols-4 gap-1.5"
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
