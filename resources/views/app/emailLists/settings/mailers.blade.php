<x-mailcoach::layout-list :title="__('mailcoach - Mailers')" :emailList="$emailList">
    <form class="form-grid" method="POST">
        @csrf
        @method('PUT')

        @if(count(config('mail.mailers')) > 1)
            <x-mailcoach::fieldset :legend="__('mailcoach - Mailers')">

            <div class="form-field">
                <label class="label">{{ __('mailcoach - Campaign mailer') }}</label>
                <div class="radio-group">
                    @foreach (config('mail.mailers') as $key => $settings)
                        <x-mailcoach::radio-field
                            name="campaign_mailer"
                            :option-value="$key"
                            :value="$emailList->campaign_mailer"
                            :label="$key"
                        />
                    @endforeach
                </div>
            </div>
            <x-mailcoach::help>{{ __('mailcoach - The mailer used for sending campaigns.') }}</x-mailcoach::help>


                <div class="form-field">
                    <label class="label">{{ __('mailcoach - Automations mailer') }}</label>
                    <div class="radio-group">
                        @foreach (config('mail.mailers') as $key => $settings)
                            <x-mailcoach::radio-field
                                name="automation_mailer"
                                :option-value="$key"
                                :value="$emailList->automation_mailer"
                                :label="$key"
                            />
                        @endforeach
                    </div>
                </div>
                <x-mailcoach::help>{{ __('mailcoach - The mailer used for sending automations.') }}</x-mailcoach::help>


                <div class="form-field">
                <label class="label">{{ __('mailcoach - Transactional mailer') }}</label>
                <div class="radio-group">
                    @foreach (config('mail.mailers') as $key => $settings)
                        <x-mailcoach::radio-field
                            name="transactional_mailer"
                            :option-value="$key"
                            :value="$emailList->transactional_mailer"
                            :label="$key"
                        />
                    @endforeach
                </div>
            </div>
            <x-mailcoach::help>{{ __('mailcoach - The mailer used for sending confirmation and welcome mails.') }}</x-mailcoach::help>

            </x-mailcoach::fieldset>
        @endif

        <div class="form-buttons">
            <x-mailcoach::button :label="__('mailcoach - Save')"/>
        </div>
    </form>
</x-mailcoach::layout-list>

