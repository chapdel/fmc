<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

    public array $filter = [];
    public string $sort = 'name';

    protected string $defaultSort;

    abstract public function getTitle(): string;
    abstract public function getView(): string;
    abstract public function getData(): array;

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.main';
    }

    public function getLayoutData(): array
    {
        return [];
    }

    public function boot()
    {
        $this->defaultSort = $this->sort;
    }

    public function getQueryString()
    {
        return [
            'filter' => ['except' => ''],
            'page' => ['except' => 1],
            'sort' => ['except' => $this->defaultSort],
        ];
    }

    public function sort(string $sort)
    {
        if ($this->sort === $sort && str_starts_with($sort, '-')) {
            return $this->sort = Str::replaceFirst('-', '', $this->sort);
        }

        if ($this->sort === $sort) {
            return $this->sort = "-" . $sort;
        }

        $this->sort = $sort;
    }

    public function setFilter(string $property, ?string $value = null)
    {
        if (is_null($value)) {
            unset($this->filter[$property]);

            return;
        }

        $this->filter[$property] = $value;
    }

    public function clearFilters()
    {
        $this->page = 1;
        $this->filter = [];
    }

    public function loadRows()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        request()->query->set('filter', $this->filter);
        request()->query->set('sort', $this->sort);

        return view(
            $this->getView(),
            $this->readyToLoad
            ? $this->getData()
            : []
        )->layout($this->getLayout(), array_merge([
            'title' => $this->getTitle(),
        ], $this->getLayoutData()));
    }
}
