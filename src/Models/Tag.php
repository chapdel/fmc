<?php

namespace Spatie\Mailcoach\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tag extends Model
{
    public $table = 'mailcoach_tags';

    public $guarded = [];

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'mailcoach_email_list_subscriber_tags', 'subscriber_id', 'tag_id');
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(EmailList::class);
    }

    public function scopeEmailList(Builder $query, EmailList $emailList): void
    {
        $query->where('email_list_id', $emailList->id);
    }
}
