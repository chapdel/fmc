<?php

namespace Spatie\Mailcoach\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class FilamentDataTableComponent extends Component implements HasTable, HasForms
{
    use LivewireFlash;
    use UsesMailcoachModels;
    use InteractsWithTable;
    use InteractsWithForms;

    #[Url]
    public bool $isTableReordering = false;

    /**
     * @var array<string, mixed> | null
     */
    #[Url]
    public ?array $tableFilters = null;

    #[Url]
    public ?string $tableGrouping = null;

    #[Url]
    public ?string $tableGroupingDirection = null;

    /**
     * @var ?string
     */
    #[Url]
    public $tableSearch = '';

    #[Url]
    public ?string $tableSortColumn = null;

    #[Url]
    public ?string $tableSortDirection = null;

    abstract protected function getTableQuery(): Builder;

    protected function isTablePaginationEnabled(): bool
    {
        return $this->getTableQuery()?->count() > $this->getTableRecordsPerPageSelectOptions()[0];
    }

    protected function getTableRecordsPerPageSelectOptions(): ?array
    {
        return [10, 25, 50, 100];
    }

    public function getView(): View
    {
        return view('mailcoach::app.table');
    }

    public function getTitle(): ?string
    {
        return null;
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.app';
    }

    public function getLayoutData(): array
    {
        return [];
    }

    public function render()
    {
        return $this->getView()->layout($this->getLayout(), array_merge([
            'title' => $this->getTitle(),
        ], $this->getLayoutData()));
    }
}
