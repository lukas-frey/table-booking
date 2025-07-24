<div class="flex items-start flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navbar>
            <flux:navbar.item :href="route('settings.profile')" wire:navigate>{{ __('Profile') }}</flux:navbar.item>
            <flux:navbar.item :href="route('settings.password')" wire:navigate>{{ __('Password') }}</flux:navbar.item>
        </flux:navbar>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
