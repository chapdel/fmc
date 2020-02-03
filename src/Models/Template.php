<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Template extends Model implements HasMedia
{
    use HasMediaTrait;

    public $table = 'mailcoach_templates';

    public $guarded = [];

    protected $casts = [
        'json' => 'json',
    ];

    public function uploads(): BelongsToMany
    {
        return $this->belongsToMany(Upload::class, 'mailcoach_template_uploads');
    }

    public function isHtmlTemplate(): bool
    {
        return $this->html && !$this->json;
    }
}
