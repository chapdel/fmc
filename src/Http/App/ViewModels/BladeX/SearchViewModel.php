<?php

namespace Spatie\Mailcoach\Http\App\ViewModels\BladeX;

use Illuminate\Http\Request;
use Spatie\BladeX\ViewModel;

class SearchViewModel extends ViewModel
{
    public string $value;

    public function __construct(Request $request)
    {
        $this->value = $request->query('filter', [])['search'] ?? '';
    }
}
