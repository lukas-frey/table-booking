@props([
    'reservation'
])

<flux:callout class="w-full mb-4 group-last:last:mb-0">
    <div class="flex flex-col gap-2">
        <flux:heading>
            {{$reservation->starts_at->format('j. n. Y')}}
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

        <flux:button href="#" size="sm" icon="x-circle" class="text-sm mr-auto">
            {{__('Cancel reservation')}}
        </flux:button>
    </div>
</flux:callout>
