<x-mailcoach::layout-list :title="__('mailcoach - Import subscribers')" :emailList="$emailList">
    @if (count($subscriberImports))
        <table class="table table-fixed mb-12">
            <thead>
            <tr>
                <th class="w-32">{{ __('mailcoach - Status') }}</th>
                <th class="w-48 th-numeric">{{ __('mailcoach - Started at') }}</th>
                <th>{{ __('mailcoach - List') }}</th>
                <th class="w-56 th-numeric">{{ __('mailcoach - Imported subscribers') }}</th>
                <th class="w-56 th-numeric">{{ __('mailcoach - Processed rows') }}</th>
                <th class="w-32 th-numeric">{{ __('mailcoach - Errors') }}</th>
                <th class="w-12"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($subscriberImports as $subscriberImport)
                <tr>
                    <td>
                        @switch($subscriberImport->status)
                            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Pending)
                                <i title="{{ __('mailcoach - Scheduled') }}" class="far fa-clock text-orange-500`"></i>
                                @break
                            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Importing)
                                <i title="{{ __('mailcoach - Importing') }}"
                                   class="fas fa-sync fa-spin text-blue-500"></i>
                                @break
                            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Completed)
                                <i title="{{ __('mailcoach - Completed') }}" class="fas fa-check text-green-500"></i>
                                @break
                        @endswitch
                    </td>
                    <td class="td-numeric">
                        {{ $subscriberImport->created_at->toMailcoachFormat() }}
                    </td>
                    <td>{{ $subscriberImport->emailList->name }}</td>
                    <td class="td-numeric">{{ $subscriberImport->subscribers()->count() }}</td>
                    <td class="td-numeric">{{ $subscriberImport->imported_subscribers_count }}</td>
                    <td class="td-numeric">{{ count($subscriberImport->errors ?? []) }}</td>
                    <td class="td-action">
                        <x-mailcoach::dropdown direction="left">
                            <ul>
                                @if (count($subscriberImport->errors ?? []))
                                    <li>
                                        <a data-no-swup
                                           href="{{ route('mailcoach.subscriberImport.downloadAttachment', [$subscriberImport, 'errorReport']) }}"
                                           download>
                                            <x-mailcoach::icon-label icon="far fa-times-circle"
                                                                     :text="__('mailcoach - Error report')"/>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a data-no-swup
                                       href="{{ route('mailcoach.subscriberImport.downloadAttachment', [$subscriberImport, 'importFile']) }}"
                                       download>
                                        <x-mailcoach::icon-label icon="far fa-file"
                                                                 :text="__('mailcoach - Uploaded file')"/>
                                    </a>
                                </li>
                                <li>
                                    <x-mailcoach::form-button
                                        :action="route('mailcoach.subscriberImport.delete', $subscriberImport)"
                                        method="DELETE" class="link-delete">
                                        <x-mailcoach::icon-label icon="far fa-trash-alt"
                                                                 :text="__('mailcoach - Delete')" :caution="true"/>
                                    </x-mailcoach::form-button>
                                </li>
                            </ul>
                        </x-mailcoach::dropdown>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <form class="form-grid" enctype="multipart/form-data" method="POST"
          action="{{ route('mailcoach.emailLists.import-subscribers', $emailList) }}">
        @csrf

        <div class="form-field">
            @error('replace_tags')
            <p class="form-error">{{ $message }}</p>
            @enderror

            <label class="label label-required" for="tags_mode">
                {{ __('mailcoach - Tags') }}
            </label>
            <div class="radio-group">
                <x-mailcoach::radio-field
                    name="replace_tags"
                    option-value="append"
                    :value="true"
                    :label="__('mailcoach - Append')"
                />
                <x-mailcoach::radio-field
                    name="replace_tags"
                    option-value="replace"
                    :label="__('mailcoach - Replace')"
                />
            </div>
        </div>

        <div class="form-buttons">
            <div class="button">
                <button class="font-semibold h-10" type="submit">
                    {{ __('mailcoach - Import subscribers') }}
                </button>
                <input onchange="this.form.submit();" class="absolute inset-0 opacity-0 text-4xl" accept=".csv, .xlsx"
                       type="file" id="file"
                       name="file" class="w-48 h-10"/>
            </div>
        </div>

        @error('file')
        <p class="form-error">{{ $message }}</p>
        @enderror

        <x-mailcoach::info>
            {!! __('mailcoach - Upload a CSV or XLSX file with these columns: email, first_name, last_name, tags <a href=":link" target="_blank">(see documentation)</a>', ['link' => 'https://spatie.be/docs/laravel-mailcoach/v5/using-mailcoach/audience#content-importing-subscribers']) !!}
        </x-mailcoach::info>
    </form>

</x-mailcoach::layout-list>
