<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class DataTable extends Component
{
    use LivewireFlash;
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use WithPagination;

    public bool $readyToLoad = false;

    public string $search = '';

    public string $sort = 'name';

    public int $perPage = 15;

    protected string $defaultSort;

    protected array $allowedFilters = [];

    abstract public function getTitle(): string;

    abstract public function getView(): string;

    abstract public function getData(Request $request): array;

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

    public function getQueryString()
    {
        return array_merge([
            'search' => ['except' => ''],
            'page' => ['except' => 1],
            'perPage' => ['except' => 15],
            'sort' => ['except' => $this->defaultSort],
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

    public function setFilter(string $property, ?string $value = null)
    {
        if (is_null($value)) {
            $this->$property = null;

            return;
        }

        $this->$property = $value;
    }

    public function clearFilters()
    {
        $this->page = 1;
        $this->search = '';
        foreach ($this->allowedFilters as $filter => $options) {
            $this->$filter = $options['except'] ?? '';
        }
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

    public function render()
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

        return view(
            $this->getView(),
            $this->readyToLoad
            ? $this->getData($request)
            : []
        )->layout($this->getLayout(), array_merge([
            'title' => $this->getTitle(),
            'hideBreadcrumbs' => true,
        ], $this->getLayoutData()));
    }
}
