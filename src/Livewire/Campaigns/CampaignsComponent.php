<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignsComponent extends Component implements HasTable, HasForms
{
    use UsesMailcoachModels;
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                self::getCampaignClass()::query()
                    ->with('emailList')
                    ->withCount(['sendsWithErrors', 'sentSends'])
            )
            ->columns([
                IconColumn::make('Status')
                    ->getStateUsing(fn (Campaign $campaign) => $campaign->status->value)
                    ->icons([
                        'heroicon-o-pencil-square' => CampaignStatus::Draft->value,
                        'heroicon-o-check-circle' => CampaignStatus::Sent->value,
                        'heroicon-o-play' => CampaignStatus::Sending->value,
                        'heroicon-o-stop-circle' => CampaignStatus::Cancelled->value,
                    ])
                    ->tooltip(fn (Campaign $campaign) => match ($campaign->status) {
                        CampaignStatus::Draft => 'Draft',
                        CampaignStatus::Sent => 'Sent',
                        CampaignStatus::Sending => 'Sending',
                        CampaignStatus::Cancelled => 'Cancelled',
                    })
                    ->color(fn (Campaign $campaign) => match ($campaign->status) {
                        CampaignStatus::Draft => '',
                        CampaignStatus::Sent => 'success',
                        CampaignStatus::Sending => 'warning',
                        CampaignStatus::Cancelled => 'danger',
                    })
                    ->alignCenter(),
                TextColumn::make('name')->sortable()->searchable()->getStateUsing(function (Campaign $campaign) {
                    return Blade::render(<<<'blade'
                    <span>
                        <span>{{ $campaign->name }}</span>
                        @if ($campaign->sends_with_errors_count)
                            <div class="flex items-center text-orange-500 text-xs mt-1">
                                <x-mailcoach::rounded-icon type="warning" icon="fas fa-info" class="mr-1" />
                                {{ $campaign->sends_with_errors_count }} {{ __mc_choice('failed send|failed sends', $campaign->sends_with_errors_count) }}
                            </div>
                        @endif
                    </span>
                blade, ['campaign' => $campaign]);
                })->html(),
                TextColumn::make('List')
                    ->url(fn (Campaign $campaign) => $campaign->emailList
                        ? route('mailcoach.emailLists.summary', $campaign->emailList)
                        : null
                    )
                    ->getStateUsing(function (Campaign $campaign) {
                        if (! $campaign->emailList) {
                            return '&ndash;';
                        }

                        return Blade::render(<<<'blade'
                        <p>{{ $campaign->emailList->name }}</p>
                        @if($campaign->usesSegment())
                            <div class="td-secondary-line">
                                {{ $campaign->getSegment()->description() }}
                            </div>
                        @endif
                    blade, ['campaign' => $campaign]);
                    })->html(),
                TextColumn::make('Emails')->getStateUsing(function (Campaign $campaign) {
                    if (! $campaign->isCancelled() && $campaign->sent_to_number_of_subscribers) {
                        return number_format($campaign->sent_to_number_of_subscribers);
                    }

                    return $campaign->sent_sends_count ? number_format($campaign->sent_sends_count) : '–';
                }),
                TextColumn::make('Opens')->getStateUsing(function (Campaign $campaign) {
                    if (! $campaign->open_rate) {
                        return '–';
                    }

                    return Blade::render(<<<blade
                    {{ number_format($campaign->unique_open_count) }}
                    <div class="td-secondary-line">{{ $campaign->open_rate / 100 }}%</div>
                blade, compact('campaign'));
                })->html(),
                TextColumn::make('Clicks')->getStateUsing(function (Campaign $campaign) {
                    return Blade::render(<<<blade
                    @if($campaign->click_rate)
                        {{ number_format($campaign->unique_click_count) }}
                        <div class="td-secondary-line">{{ $campaign->click_rate / 100 }}%</div>
                    @else
                        –
                    @endif
                blade, compact('campaign'));
                })->html(),
                TextColumn::make('Sent')->getStateUsing(function (Campaign $campaign) {
                    return Blade::render(<<<'blade'
                    @if($campaign->isSent())
                        {{ optional($campaign->sent_at)->toMailcoachFormat() }}
                    @elseif($campaign->isSending())
                        {{ optional($campaign->updated_at)->toMailcoachFormat() }}
                        <div class="td-secondary-line">
                            {{ __mc('In progress') }}
                        </div>
                    @elseif($campaign->isScheduled())
                        {{ optional($campaign->scheduled_at)->toMailcoachFormat() }}
                        <div class="td-secondary-line">
                            {{ __mc('Scheduled') }}
                        </div>
                    @else
                        –
                    @endif
                blade, compact('campaign'));
                })->html(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'sent' => 'Sent',
                        'scheduled' => 'Scheduled',
                        'draft' => 'Draft',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            'sent' => $query->sent(),
                            'scheduled' => $query->scheduled(),
                            'draft' => $query->draft(),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('Duplicate')
                        ->action(fn (Campaign $record) => $this->duplicateCampaign($record))
                        ->icon('heroicon-o-clipboard')
                        ->label(__mc('Duplicate')),
                    Action::make('Delete')
                        ->action(fn (Campaign $record) => $record->delete())
                        ->requiresConfirmation()
                        ->label(__mc('Delete'))
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ]),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(fn (Collection $records) => $records->each->delete()),
            ])
            ->recordUrl(function (Campaign $campaign) {
                if ($campaign->isSent() || $campaign->isSending() || $campaign->isCancelled()) {
                    return route('mailcoach.campaigns.summary', $campaign);
                }

                if ($campaign->isScheduled()) {
                    return route('mailcoach.campaigns.delivery', $campaign);
                }

                return route('mailcoach.campaigns.content', $campaign);
            });
    }

    public function duplicateCampaign(Campaign $campaign)
    {
        $this->authorize('create', self::getCampaignClass());

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $duplicateCampaign */
        $duplicateCampaign = self::getCampaignClass()::create([
            'name' => __mc('Duplicate of').' '.$campaign->name,
            'subject' => $campaign->subject,
            'template_id' => $campaign->template_id,
            'email_list_id' => $campaign->email_list_id,
            'html' => $campaign->html,
            'structured_html' => $campaign->structured_html,
            'utm_tags' => $campaign->utm_tags,
            'last_modified_at' => now(),
            'segment_class' => $campaign->segment_class,
            'segment_id' => $campaign->segment_id,
        ]);

        $duplicateCampaign->update([
            'segment_description' => $duplicateCampaign->getSegment()->description(),
        ]);

        flash()->success(__mc('Campaign :campaign was duplicated.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $duplicateCampaign);
    }

    public function render()
    {
        return view('mailcoach::app.campaigns.index')
            ->layout('mailcoach::app.layouts.app', [
                'title' => __mc('Campaigns'),
                'hideBreadcrumbs' => true,
            ]);
    }
}
