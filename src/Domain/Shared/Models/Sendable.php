<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

/**
 * @property ?string $name
 * @property string $html
 * @property ?string $email_html
 * @property ?string $webview_html
 * @property string $mailable_class
 * @property bool $utm_tags
 * @property ?string $subject
 * @property int $sent_to_number_of_subscribers
 * @property ?bool $disable_webview
 * @property ?\Spatie\Mailcoach\Domain\Content\Models\ContentItem $contentItem
 */
abstract class Sendable extends Model
{
    use HasFactory;
    use HasTemplate;
    use HasUuid;
    use UsesMailcoachModels;

    protected $guarded = [];

    public $baseCasts = [
        'id' => 'int',
        'utm_tags' => 'boolean',
        'open_rate' => 'integer',
        'click_rate' => 'integer',
        'sent_at' => 'datetime',
        'requires_confirmation' => 'boolean',
        'statistics_calculated_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'mailable_arguments' => 'array',
    ];

    public function contentItem(): MorphOne
    {
        return $this->morphOne(self::getContentItemClass(), 'model');
    }

    public function contentItems(): MorphMany
    {
        return $this->morphMany(self::getContentItemClass(), 'model');
    }

    public function useMailable(string $mailableClass, array $mailableArguments = []): self
    {
        $this->contentItem->useMailable($mailableClass, $mailableArguments);

        return $this;
    }

    public function hasCustomMailable(): bool
    {
        return $this->contentItem->hasCustomMailable();
    }

    public function contentFromMailable(): string
    {
        return $this->contentItem->contentFromMailable();
    }

    public function pullSubjectFromMailable(): void
    {
        $this->contentItem->pullSubjectFromMailable();
    }

    public function htmlWithInlinedCss(): string
    {
        return $this->contentItem->htmlWithInlinedCss();
    }

    public function from(string $email, string $name = null): static
    {
        $this->contentItem->from($email, $name);

        return $this;
    }

    public function replyTo(string $email, string $name = null): static
    {
        $this->contentItem->replyTo($email, $name);

        return $this;
    }

    public function subject(string $subject): static
    {
        $this->contentItem->setSubject($subject);

        return $this;
    }

    public function getFromEmail(Send $send = null): string
    {
        return $this->contentItem->getFromEmail($send);
    }

    public function getFromName(Send $send = null): ?string
    {
        return $this->contentItem->getFromName($send);
    }

    public function content(string $html): self
    {
        $this->ensureUpdatable();

        $this->contentItem->content($html);

        return $this;
    }

    public function htmlLinks(): Collection
    {
        return $this->contentItem->htmlLinks();
    }

    abstract public function isReady(): bool;

    public function isEditable(): bool
    {
        return true;
    }

    public function getCasts()
    {
        return array_merge($this->baseCasts, $this->casts ?? []);
    }

    protected function ensureSendable()
    {
    }

    abstract public function sendTestMail(string|array $emails): void;

    abstract public function webviewUrl(): string;

    protected function ensureUpdatable(): void
    {
    }
}
