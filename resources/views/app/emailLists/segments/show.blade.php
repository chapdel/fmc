<div class="mt-6">
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'details')" :active="$tab === 'details'">
                {{ __mc('Segment details') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'population')" :active="$tab === 'population'">
                <x-mailcoach::icon-label invers>
                    <x-slot:count>
                        <livewire:mailcoach::segment-population-count lazy :segment="$segment" />
                    </x-slot:count>
                    <x-slot:text>
                        {{ __mc('Population') }}
                    </x-slot:text>
                </x-mailcoach::icon-label>
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    @if ($tab === 'details')
        <form
            wire:submit="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
            method="POST"
        >
        <x-mailcoach::card>
            @csrf
            @method('PUT')

            <x-mailcoach::text-field wrapper-class="md:max-w-3xl" :label="__mc('Name')" name="name" wire:model="name" type="name" required />

            <div class="form-field md:max-w-3xl">
                <label class="label label-required">
                    {{ __mc('Conditions') }}
                </label>
                <livewire:mailcoach::condition-builder :email-list="$emailList" :storedConditions="$segment->stored_conditions->castToArray()" />
            </div>

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Save segment')" />
            </x-mailcoach::form-buttons>
        </x-mailcoach::card>

        <x-mailcoach::fieldset class="mt-6" card :legend="__mc('Usage in Mailcoach API')">
            <div>
                <x-mailcoach::help>
                    {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                    'resourceName' => 'segment uuid',
                    'resource' => 'segment',
                ]) !!}
                    <p class="mt-4">
                        <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$segment->uuid"></x-mailcoach::code-copy>
                    </p>
                </x-mailcoach::help>
            </div>
        </x-mailcoach::fieldset>
        </form>
    @endif

    @if($tab === 'population')
        <livewire:mailcoach::segment-subscribers :emailList="$emailList" :segment="$segment" />
    @endif
</div>
