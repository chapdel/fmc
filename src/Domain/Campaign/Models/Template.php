<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\TemplateFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Template extends Model implements HasHtmlContent
{
    use UsesMailcoachModels;
    use HasFactory;

    public $table = 'mailcoach_templates';

    public $guarded = [];

    protected $casts = [
        'json' => 'json',
        'contains_placeholders' => 'boolean',
    ];

    public static function booted()
    {
        static::saving(function (Template $template) {
            $template->contains_placeholders = $template->containsPlaceHolders();
        });
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany($this->getCampaignClass());
    }

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

        return self::getTemplateClass()::where($field, $value)->firstOrFail();
    }

    protected static function newFactory(): TemplateFactory
    {
        return new TemplateFactory();
    }

    public function containsPlaceHolders(): bool
    {
        return (new TemplateRenderer($this->getHtml()))->containsPlaceHolders();
    }

    public function placeHolderNames(): array
    {
        return (new TemplateRenderer($this->getHtml()))->placeHolderNames();
    }
}
