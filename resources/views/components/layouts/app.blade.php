@props([
    'centered' => false
])
<x-layouts.app.base :title="$title ?? null" :centered="$centered">
    {{ $slot }}
</x-layouts.app.base>
