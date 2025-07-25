@props([
    'reservation'
])

<flux:callout
    @class([
        "w-full mb-4 group-last:last:mb-0",
        "data-highlight:animate-highlight data-highlight:border-rose-400 data-highlight:bg-rose-50 dark:data-highlight:border-rose-400/20 dark:data-highlight:bg-rose-500/20",
        "border-dashed opacity-50" => $reservation->cancelled_at
    ])
    wire:key="reservation-{{$reservation->getKey()}}"
    id="reservation-{{$reservation->getKey()}}"
    x-data="{
        animate: (element) => {
            if (window.location.hash === '#reservation-{{$reservation->getKey()}}') {
                element.setAttribute('data-highlight', 'data-highlight')
            } else {
                element.removeAttribute('data-highlight')
            }
        }
    }"
    x-init="animate($el)"
    x-on:hashchange.window="animate($el)"
>
    <div class="flex flex-col gap-2">

        <flux:heading class="w-full flex items-center justify-between gap-2">
            <span @class(['line-through' => $reservation->cancelled_at])>{{$reservation->starts_at->format('j. n. Y')}}</span>

            @if($reservation->cancelled_at)
                <flux:badge size="sm">{{__('Cancelled')}}</flux:badge>
            @elseif(now()->isBetween($reservation->starts_at, $reservation->ends_at))
                <flux:badge size="sm" color="lime">{{__('Ongoing')}}</flux:badge>
            @endif
        </flux:heading>
        <div class="flex flex-row flex-wrap gap-4">
            <span class="text-zinc-500 dark:text-zinc-400 inline-flex items-center gap-1">
                <flux:icon name="clock" class="size-4"/>
                <span>{{$reservation->starts_at->format('H:i')}} - {{$reservation->ends_at->format('H:i')}}</span>
            </span>

            <span class="text-zinc-500 dark:text-zinc-400 inline-flex items-center gap-1">
                <flux:icon name="users" class="size-4"/>
                <span>{{trans_choice(':count guest|:count guests', $reservation->guests)}}</span>
            </span>
        </div>

        @if(!$reservation->cancelled_at && ! now()->isBetween($reservation->starts_at, $reservation->ends_at))
            <flux:modal.trigger name="cancel-reservation-{{$reservation->getKey()}}">
                <flux:button size="sm" type="submit" icon="x-circle" class="text-sm mr-auto mt-2">
                    {{__('Cancel reservation')}}
                </flux:button>
            </flux:modal.trigger>

            @teleport('body')
            <flux:modal name="cancel-reservation-{{$reservation->getKey()}}" :show="$errors->isNotEmpty()" focusable
                        class="max-w-lg">
                <form method="POST" action="{{route('reservations.cancel', ['reservation' => $reservation])}}"
                      class="space-y-6">
                    @csrf
                    <div>
                        <flux:heading
                            size="lg">{{ __('Are you sure you want to cancel your reservation?') }}</flux:heading>

                        <flux:subheading>
                            {{ __('Once the reservation is cancelled, it can no longer be reversed.') }}
                        </flux:subheading>
                    </div>

                    <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                        <flux:modal.close>
                            <flux:button variant="filled">{{ __('I changed my mind') }}</flux:button>
                        </flux:modal.close>

                        <flux:button variant="danger" type="submit">{{ __('Cancel reservation') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
            @endteleport
        @endif
    </div>
</flux:callout>
