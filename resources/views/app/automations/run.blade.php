<form
    class="form-grid"
    method="POST"
>
    @csrf
    @method('PUT')

    <x-mailcoach::fieldset :legend="__('mailcoach - Interval')">
        @if ($automation->interval === '1 minute')
            <x-mailcoach::warning>
                {{ __('mailcoach - An interval of 1 minute can generate a lot of queued jobs for subscribers pending in an action. Make sure you really need this granularity.') }}
            </x-mailcoach::warning>
        @endif

        <div class="flex items-end">

            <x-mailcoach::select-field
                name="automation.interval"
                wire:model="automation.interval"
                :options="[
                    '1 minute' => 'Every minute',
                    '10 minutes' => 'Every 10 minutes',
                    '1 hour' => 'Hourly',
                    '1 day' => 'Daily',
                    '1 week' => 'Weekly',
                ]"
                required
            />

            <x-mailcoach::button class="ml-1" :label="__('mailcoach - Save')" wire:click.prevent="save" />
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset :legend="__('mailcoach - Run automation')">
        @if ($error)
            <div class="alert alert-error shadow-lg mb-6">
                <div class="max-w-layout mx-auto grid gap-1">
                    <div class="flex items-baseline">
                        <span class="w-6"><i class="fas fa-times opacity-50"></i></span>
                        <span class="ml-2 text-sm">
                            {{ $error }}
                        </span>
                    </div>
                </div>
            </div>
        @endif
        @if ($automation->actions->filter(fn ($action) => $action->action::class === \Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction::class)->count() === 0)
            <x-mailcoach::warning>
                {{ __('mailcoach - Your automation does not contain a "Halt" action. This will cause the automation to keep running for subscribers in the last action and could generate more queued jobs than desired. Do this only if you intend to add more actions later.') }}
            </x-mailcoach::warning>
        @endif
        <div>
        @if ($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::STARTED)
            <button class="button bg-gradient-to-r from-orange-400 to-orange-500" type="button" wire:click.prevent="pause">
                <span class="flex items-center">
                    <i class="fas fa-pause text-sm"></i>
                    <span class="ml-2">{{ __('mailcoach - Pause') }}</span>
                </span>
            </button>
        @else
            <button class="button bg-gradient-to-r from-green-400 to-green-500" type="button" wire:click.prevent="start">
                <span class="flex items-center">
                    <i class="fas fa-play text-sm"></i>
                    <span class="ml-2">{{ __('mailcoach - Start') }}</span>
                </span>
            </button>
        @endif
        </div>
    </x-mailcoach::fieldset>
</form>
