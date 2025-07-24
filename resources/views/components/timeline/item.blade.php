@php
    use Illuminate\Support\Carbon;
@endphp

@props([
    'date',
    'variant' => 'default',
])

<div {{$attributes->class([
    "flex gap-4 group"
])}}>
    <div class="flex flex-col items-center">
        <div class="flex flex-col items-center gap-0.5">
            <span @class([
                "uppercase text-sm",
                "text-zinc-400 dark:text-zinc-500" => $variant === 'default',
                "text-zinc-300 dark:text-zinc-400" => $variant === 'ghost',
            ])>
                {{Carbon::parse($date)->format('M')}}
            </span>
            <span @class([
                "flex items-center justify-center text-lg rounded-full w-8 h-8",
                "text-white bg-rose-400 dark:bg-rose-600/80 dark:text-zinc-200" => $variant === 'default',
                "text-zinc-300 border border-zinc-200 border-dashed" => $variant === 'ghost',
            ])>
                {{Carbon::parse($date)->format('j')}}
            </span>
        </div>
        <div @class([
            "border-l h-full my-2",
            "border-l-zinc-300 dark:border-l-white/15" => $variant === 'default',
            "border-l-zinc-200 dark:border-l-white/10 border-dashed" => $variant === 'ghost',
        ])></div>
    </div>
    <div class="flex flex-col w-full">
        {{ $slot }}
    </div>
</div>
