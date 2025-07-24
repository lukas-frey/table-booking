@props([
    'title',
    'description',
    'icon' => null,
    'iconVariant' => 'solid',
    'variant' => 'default',
])

<div @class([
    "flex w-full flex-col",
    "text-center" => $variant === 'auth',
    "mb-6" => $variant === 'default',
])>
    @if($icon)
        <div class="flex flex-col items-center gap-2 font-medium">
            <span class="bg-zinc-100 flex p-2 mb-1 items-center justify-center rounded-xl">
                <flux:icon :variant="$iconVariant" :name="$icon"
                           class="size-9 fill-current text-zinc-500 dark:text-white"/>
            </span>
        </div>
    @endif

    <flux:heading size="xl" level="1">{{ $title }}</flux:heading>
    <flux:subheading
        @class([
        'mb-6' => $variant === 'default'
    ])
        :size="$variant === 'default' ? 'lg' : 'base'"
    >
        {{ $description }}
    </flux:subheading>

    @if($variant === 'default')
        <flux:separator variant="subtle"/>
    @endif
</div>
