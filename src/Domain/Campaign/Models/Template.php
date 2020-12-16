<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Support\Traits\UsesMailcoachModels;

class Template extends Model implements HasHtmlContent
{
    use UsesMailcoachModels, HasFactory;

    public $table = 'mailcoach_templates';

    public $guarded = [];

    protected $casts = [
        'json' => 'json',
    ];

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getStructuredHtml(): ?string
    {
        return $this->structured_html;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getTemplateClass()::where($field, $value)->firstOrFail();
    }
}
