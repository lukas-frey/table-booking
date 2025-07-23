<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Thank you for your reservation!')"
        :description="__('Please check your e-mail for the confirmation of your reservation.')"
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
</div>
