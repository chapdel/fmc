<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class DataTableComponent extends Component
{
    use LivewireFlash;
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use WithPagination;

    public bool $readyToLoad = false;

    public string $search = '';

    public string $sort = 'name';

    public int $perPage = 15;

    public array $selectedRows = [];

    public bool $selectedAll = false;

    public string $bulkAction = '';

    protected string $defaultSort;

    protected array $allowedFilters = [];

    abstract public function getTitle(): string;

    abstract public function getView(): string;

    abstract public function getData(Request $request): array;

    public function getQuery(Request $request): ?QueryBuilder
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

    public function boot()
    {
        $this->defaultSort = $this->sort;

        foreach ($this->allowedFilters as $filter => $options) {
            $this->$filter = $options['except'] ?? '';
        }
    }

    public function queryString(): array
    {
        return array_merge([
            'search',
            'page',
            'perPage',
            'sort',
        ], $this->allowedFilters);
    }

    public function sort(string $sort)
    {
        if ($this->sort === $sort && str_starts_with($sort, '-')) {
            return $this->sort = Str::replaceFirst('-', '', $this->sort);
        }

        if ($this->sort === $sort) {
            return $this->sort = '-'.$sort;
        }

        $this->sort = $sort;
    }

    public function setFilter(string $property, string $value = null)
    {
        $this->resetPage();

        if (is_null($value)) {
            $this->$property = null;

            return;
        }

        $this->$property = $value;
    }

    public function clearFilters()
    {
        $this->resetPage();
        $this->search = '';
        foreach ($this->allowedFilters as $filter => $options) {
            $this->$filter = $options['except'] ?? '';
        }
    }

    public function replaceFilter(string $key, string|int $value): void
    {
        if (! array_key_exists($key, $this->allowedFilters)) {
            return;
        }

        $this->$key = $value;
    }

    public function addFilter(string $key, string|int $value): void
    {
        if (! array_key_exists($key, $this->allowedFilters)) {
            return;
        }

        $currentFilters = array_filter(explode(',', $this->$key));
        $currentFilters[] = $value;
        $newFilters = array_unique($currentFilters);

        $this->$key = implode(',', $newFilters);
    }

    public function removeFilter(string $key, $value): void
    {
        if (! array_key_exists($key, $this->allowedFilters)) {
            return;
        }

        $currentFilters = array_filter(explode(',', $this->$key));
        $newFilters = array_filter($currentFilters, fn (string $filter) => $filter !== $value);

        $this->$key = implode(',', $newFilters);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function isFiltering(): bool
    {
        return collect($this->allowedFilters)
            ->put('search', [])
            ->filter(function ($options, string $filter) {
                return $this->$filter !== ($options['except'] ?? '');
            })
            ->count() > 0;
    }

    public function loadRows()
    {
        $this->readyToLoad = true;
    }

    public function selectAll(bool $withoutPagination = false): void
    {
        $this->selectedRows = [];

        if ($this->selectedAll && ! $withoutPagination) {
            $this->selectedAll = false;

            return;
        }

        /** @var ?\Spatie\QueryBuilder\QueryBuilder $query */
        $query = $this->getQuery($this->buildRequest());

        if (! $query) {
            return;
        }

        $this->selectedAll = true;

        if ($withoutPagination) {
            $this->selectedRows = $query
                ->reorder('id')
                ->pluck('id')
                ->toArray();

            return;
        }

        $this->selectedRows = Arr::pluck($query->paginate()->items(), 'id');
    }

    public function select(string|int $row): void
    {
        $this->selectedAll = false;

        if (in_array($row, $this->selectedRows)) {
            array_splice($this->selectedRows, array_search($row, $this->selectedRows));

            return;
        }

        $this->selectedRows[] = $row;
    }

    public function resetSelect(): void
    {
        $this->selectedRows = [];
        $this->bulkAction = '';
        $this->selectedAll = false;
    }

    public function formatExportRow(Model $model): array
    {
        if (method_exists($model, 'toExportRow')) {
            return $model->toExportRow();
        }

        return $model->toArray();
    }

    public function export(): StreamedResponse
    {
        $query = $this->getQuery($this->buildRequest());

        if (! $query) {
            throw new Exception('You must implement getQuery to use export.');
        }

        if ($this->selectedRows) {
            $query->whereIn('id', $this->selectedRows);
        }

        return response()->streamDownload(function () use ($query) {
            $csv = SimpleExcelWriter::streamDownload($this->getTitle().' export.csv');

            // Get the first query result to determine header
            $header = array_keys($this->formatExportRow($query->first()));
            $csv->addHeader($header);

            $query->each(function (Model $model) use ($csv) {
                $csv->addRow($this->formatExportRow($model));

                flush();
            });

            $csv->close();
        }, ($this->getTitle().' export.csv'), [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function buildRequest(): Request
    {
        $request = clone request();
        $request->query->set('sort', $this->sort);
        $request->query->set('per_page', $this->perPage);
        $request->query->set(
            'filter',
            collect($this->allowedFilters)
                ->keys()
                ->add('search')
                ->mapWithKeys(fn (string $filter) => [$filter => addcslashes($this->$filter, '%')])
                ->filter(fn ($value) => ! empty($value))
        );

        return $request;
    }

    public function render()
    {
        return view(
            $this->getView(),
            $this->readyToLoad ? $this->getData($this->buildRequest()) : []
        )->layout($this->getLayout(), array_merge([
            'title' => $this->getTitle(),
            'hideBreadcrumbs' => true,
        ], $this->getLayoutData()));
    }
}
