<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class Template extends Model implements HasHtmlContent
{
    use UsesMailcoachModels;

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
