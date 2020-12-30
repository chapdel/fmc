<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\RenderTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\InvalidTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\Replacers\TransactionalMailReplacer;

class TransactionalMailTemplate extends Model
{
    public $table = 'mailcoach_transactions_mail_templates';

    use HasFactory;

    public $guarded = [];

    public $casts = [
        'store_mail' => 'boolean',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
        'from' => 'string',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'allows_blade_compilation' => 'boolean',
        'replacers' => 'array',
    ];

    public function isValid(): bool
    {
        try {
            $this->validate();
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    public function validate(): void
    {
        $mailable = $this->getMailable();

        $mailable->render();
    }

    public function getMailable(): Mailable
    {
        $mailableClass = $this->test_using_mailable;

        if (! class_exists($mailableClass)) {
            throw InvalidTemplate::mailableClassNotFound($this);
        }

        $traits = class_uses_recursive($mailableClass);

        if (! in_array(UsesMailcoachTemplate::class, $traits)) {
            throw InvalidTemplate::mailableClassNotValid($this);
        }

        return $mailableClass::testInstance();
    }

    public function replacers(): Collection
    {
        return collect($this->replacers ?? [])
            ->map(function(string $replacerName): TransactionalMailReplacer {
                $replacerClass = config("mailcoach.transactional.replacers.{$replacerName}");

                if (is_null($replacerClass)) {
                    throw InvalidTemplate::replacerNotFound($this, $replacerName);
                }

                if (! is_a($replacerClass, TransactionalMailReplacer::class, true)) {
                    throw InvalidTemplate::invalidReplacer($this, $replacerName, $replacerClass);
                }

                return app()->make($replacerClass);
            });
    }

    public function render(Mailable $mailable): string
    {
        $action = Config::getTransactionalActionClass('render_template', RenderTemplateAction::class);

        return $action->execute($this, $mailable);
    }
}
