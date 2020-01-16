<?php

namespace Spatie\Mailcoach\Http\App\ViewModels\BladeX;

use Spatie\BladeX\ViewModel;
use Spatie\QueryString\QueryString;

class FilterViewModel extends ViewModel
{
    private QueryString $queryString;

    private string $attribute;

    private string $activeOn;

    public function __construct(QueryString $queryString, string $attribute, string $activeOn)
    {
        $this->queryString = $queryString;

        $this->attribute = $attribute;

        $this->activeOn = $activeOn;
    }

    public function href(): string
    {
        return $this->activeOn === ""
            ? $this->queryString->disable("filter[{$this->attribute}]")
            : $this->queryString->enable("filter[{$this->attribute}]", $this->activeOn);
    }

    public function active(): bool
    {
        return $this->activeOn === ""
            ? ! $this->queryString->isActive("filter[{$this->attribute}]")
            : $this->queryString->isActive("filter[{$this->attribute}]", $this->activeOn);
    }
}
