@props([
    'centered' => false
])
<x-layouts.app.base :title="$title ?? null" :centered="$centered">
{{--    <flux:main>--}}
        {{ $slot }}
{{--    </flux:main>--}}
</x-layouts.app.base>
