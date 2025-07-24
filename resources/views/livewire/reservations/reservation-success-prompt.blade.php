<div class="flex flex-col gap-6">
    <x-heading
        :title="__('Thank you for your reservation!')"
        :description="__('Please check your e-mail for the confirmation of your reservation.')"
        variant="auth"
    />

    <flux:callout>
        <flux:callout.text class="space-y-3">
            <div class="flex justify-between">
                <span>{{__('Name')}}</span>
                <span class="font-bold">{{$this->reservation->user->name}}</span>
            </div>

            <div class="flex justify-between">
                <span>{{__('Date')}}</span>
                <span
                    class="font-bold">{{$this->reservation->starts_at->format('j. n. Y H:i')}} - {{$this->reservation->ends_at->format('H:i')}}</span>
            </div>

            <div class="flex justify-between">
                <span>{{__('Amount of guests')}}</span>
                <span class="font-bold">{{$this->reservation->guests}}</span>
            </div>
        </flux:callout.text>
    </flux:callout>

    <div class="flex justify-end space-x-2">
        <flux:button href="{{route('reservations.create')}}" variant="subtle">{{ __('Create another') }}</flux:button>
        <flux:button href="{{route('reservations.index')}}">{{ __('View reservations') }}</flux:button>
    </div>
</div>
