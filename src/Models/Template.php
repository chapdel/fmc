<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Mailcoach\Models\Concerns\HasHtmlContent;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Template extends Model implements HasMedia, HasHtmlContent
{
    use HasMediaTrait;

    public $table = 'mailcoach_templates';

    public $guarded = [];

    protected $casts = [
        'json' => 'json',
    ];

    public function isHtmlTemplate(): bool
    {
        return $this->html && !$this->json;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getStructuredHtml(): ?string
    {
        return $this->structured_html;
    }
}
