<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Make a reservation')" :description="__('Please fill out the form below to reserve a table')" />

    <!-- Session Status -->
{{--    <x-auth-session-status class="text-center" :status="session('status')" />--}}

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Guests count -->
        <flux:input
            wire:model="guests"
            :label="__('Amount of guests')"
            type="number"
            autofocus
            required
        />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Reserve table now') }}</flux:button>
        </div>
    </form>
    {{-- Success is as dangerous as failure. --}}
</div>
