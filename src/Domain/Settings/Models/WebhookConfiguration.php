<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ParagonIE\CipherSweet\CipherSweet as CipherSweetEngine;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Observers\ModelObserver;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class WebhookConfiguration extends Model
{
    use HasUuid;
    use UsesMailcoachModels;
    use HasFactory;
    use UsesCipherSweet;

    public $guarded = [];

    public $table = 'mailcoach_webhook_configurations';

    public $casts = [
        'use_for_all_lists' => 'boolean',
    ];

    public function emailLists(): BelongsToMany
    {
        return $this->belongsToMany(
            EmailList::class,
            'mailcoach_webhook_configuration_email_lists',
            'email_list_id',
            'webhook_configuration_id',
        );
    }

    protected static function bootUsesCipherSweet()
    {
        if (! config('mailcoach.encryption.enabled')) {
            return;
        }

        static::observe(ModelObserver::class);

        static::$cipherSweetEncryptedRow = new EncryptedRow(
            app(CipherSweetEngine::class),
            (new static())->getTable()
        );

        static::configureCipherSweet(static::$cipherSweetEncryptedRow);
    }

    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow->addTextField('secret');
    }
}
