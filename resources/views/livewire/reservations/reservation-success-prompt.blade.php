<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Thank you for your reservation!')" :description="__('Please check your e-mail for the confirmation of your reservation.')" />

    <flux:callout>
        <flux:callout.text class="space-y-3">
            <div class="flex justify-between">
                <span>Name</span>
                <span class="font-bold">John Doe</span>
            </div>

            <div class="flex justify-between">
                <span>Date</span>
                <span class="font-bold">{{today()->format('j. n .Y H:i')}} - 14:00</span>
            </div>

            <div class="flex justify-between">
                <span>Amount of guests</span>
                <span class="font-bold">4</span>
            </div>
        </flux:callout.text>
    </flux:callout>
</div>
