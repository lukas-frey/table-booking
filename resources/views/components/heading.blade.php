@props([
    'title',
    'description',
    'icon' => null,
    'iconVariant' => 'solid'
])

<div class="flex w-full flex-col text-center">
    @if($icon)
        <div class="flex flex-col items-center gap-2 font-medium">
            <span class="bg-zinc-100 flex p-2 mb-1 items-center justify-center rounded-xl">
                <flux:icon :variant="$iconVariant" :name="$icon" class="size-9 fill-current text-zinc-500 dark:text-white"/>
            </span>
        </div>
    @endif
    <flux:heading size="xl">{{ $title }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
