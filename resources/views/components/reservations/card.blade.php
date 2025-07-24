@props([
    'reservation'
])

<flux:callout class="w-full mb-4 group-last:last:mb-0">
    <div class="flex flex-col gap-2">

        <flux:heading class="w-full flex items-center justify-between gap-2">
            <span>{{$reservation->starts_at->format('j. n. Y')}}</span>

            @if(now()->isBetween($reservation->starts_at, $reservation->ends_at))
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

        @if(! now()->isBetween($reservation->starts_at, $reservation->ends_at))
            <flux:button href="#" size="sm" icon="x-circle" class="text-sm mr-auto">
                {{__('Cancel reservation')}}
            </flux:button>
        @endif
    </div>
</flux:callout>
