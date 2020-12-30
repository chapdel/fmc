<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Blade;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\InvalidTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns\UsesMailcoachTemplate;

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

    public function render(array $arguments): string
    {
        $generated = Blade::compileString($this->body);

        ob_start() and extract($arguments, EXTR_SKIP);

        // We'll include the view contents for parsing within a catcher
        // so we can avoid any WSOD errors. If an exception occurs we
        // will throw it out to the exception handler.
        try {
            eval('?>'.$generated);
        }
        // If we caught an exception, we'll silently flush the output
        // buffer so that no partially rendered views get thrown out
        // to the client and confuse the user with junk.
        catch (Exception $e) {
            ob_get_clean();

            throw $e;
        }

        $content = ob_get_clean();

        return $content;
    }
}
