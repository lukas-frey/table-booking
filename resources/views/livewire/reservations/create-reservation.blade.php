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
                state:  $wire.entangle('date'),
                minDate: @js(today()),
                maxDate: @js(today()->addDays(config('app.reservation_max_days'))),
                disabledDates: @js($this->getDisabledDates())
            })"
                 class="w-full"
                 wire:ignore
            ></div>
            <flux:error name="date"></flux:error>
        </flux:field>

        <flux:input
            wire:model="time"
            :label="__('Select a time')"
            type="time"
            step="1800"
            min="12:00"
            max="18:00"
            list="timeslots"
            required
        />

        <datalist id="timeslots">
            <option value="12:00"></option>
            <option value="12:30"></option>
        </datalist>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Reserve table now') }}</flux:button>
        </div>
    </form>
</div>
