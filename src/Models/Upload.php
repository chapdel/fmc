<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Upload extends Model implements HasMedia
{
    use HasMediaTrait;

    public $table = 'mailcoach_uploads';

    public $guarded = [];

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('image')
            ->fit(
                Manipulations::FIT_MAX,
                config('mailcoach.editor.uploads.max_width', 1500),
                config('mailcoach.editor.uploads.max_height', 1500)
            )
            ->nonQueued();
    }

    public function templates(): BelongsToMany
    {
        return $this->belongsToMany(Template::class, 'mailcoach_template_uploads');
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'mailcoach_campaign_uploads');
    }
}
