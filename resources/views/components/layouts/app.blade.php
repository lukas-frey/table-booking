@php
    use Illuminate\View\ComponentSlot;
@endphp

@props([
    'withHeader' => true,
    'aside' => new ComponentSlot(),
])

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white antialiased dark:bg-neutral-950">
@if($withHeader)
    <x-navigation/>
@endif

<div @class([
    "bg-background flex min-h-svh flex-col items-center gap-6 p-6 md:p-10",
    "justify-center" => ! $withHeader,
    "-mt-14 pt-20! md:pt-24!" => $withHeader,
    "grid grid-cols-1 md:grid-cols-2" => $aside->isNotEmpty(),
])>
    @if($aside->isNotEmpty())
        <div {{$aside->attributes->class(["flex w-full md:max-w-sm flex-col ml-auto gap-2"])}}>
            {{$aside}}
        </div>
    @endif
    <div class="flex w-full md:max-w-sm flex-col gap-2">
        <div class="flex flex-col gap-6">
            {{ $slot }}
        </div>
    </div>
</div>
@fluxScripts
</body>
</html>
