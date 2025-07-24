@php
    use Illuminate\Support\Carbon;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-4">
            <x-heading
                :title="__('Your reservations')"
                :description="__('This is where you can view all your upcoming reservations.')"
            />
        </div>

        <div>
            @if($reservationsGroupedByDate->isNotEmpty())
                <x-timeline.item variant="ghost" :date="now()">
                    <div class="flex flex-col gap-2 mb-10">
                        <flux:heading>{{__('Today')}}</flux:heading>

                        <flux:text>{{__('Why not squeeze in a relaxed dinner tonight?')}}</flux:text>
                        <flux:text>{{__('Enjoy a great day with good company and delicious food.')}}</flux:text>

                        <flux:button size="sm" href="{{route('reservations.create')}}">
                            {{__('Reserve a table now')}}
                        </flux:button>
                    </div>
                </x-timeline.item>
            @else
                <x-timeline.item variant="ghost" :date="now()">
                    <div class="flex flex-col gap-2 mb-12 group-last:last:mb-0">
                        <flux:heading>{{__('Today')}}</flux:heading>

                        <flux:text>{{__('You don’t have any plans lined up yet.')}}</flux:text>
                        <flux:text>{{__('Treat yourself to a delightful dinner and make a reservation today!')}}</flux:text>

                        <flux:button size="sm" href="{{route('reservations.create')}}">
                            {{__('Reserve a table now')}}
                        </flux:button>
                    </div>
                </x-timeline.item>
            @endif

            @foreach($reservationsGroupedByDate as $date => $reservations)
                <x-timeline.item :date="$date">
                    @foreach($reservations as $reservation)
                        <x-reservations.card :reservation="$reservation"/>
                    @endforeach
                </x-timeline.item>
            @endforeach
        </div>
    </div>
</div>
