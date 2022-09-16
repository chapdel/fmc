<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\CouldNotFindTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

/** @mixin \Illuminate\Mail\Mailable */
trait UsesMailcoachTemplate
{
    use StoresMail;
    use UsesMailcoachModels;

    public function template(
        string $name,
        array $replacements = [],
        array $fields = [],
    ): self
    {
        /** @var TransactionalMailTemplate $template */
        $template = self::getTransactionalMailTemplateClass()::firstWhere('name', $name);

        if (! $template) {
            throw CouldNotFindTemplate::make($name, $this);
        }

        $this->setSubject($template, $replacements);

        if ($template->from) {
            $this->from($template->from);
        }

        foreach ($template->to ?? [] as $to) {
            $this->to($to);
        }

        foreach ($template->cc ?? [] as $cc) {
            $this->cc($cc);
        }

        foreach ($template->bcc ?? [] as $bcc) {
            $this->bcc($bcc);
        }

        $content = $template->render($this, $replacements, $fields);

        $this->view('mailcoach::mails.transactionalMails.template', compact('content'));

        return $this;
    }

    protected function executeReplacers(
        string $text,
        TransactionalMailTemplate $template,
        Mailable $mailable
    ): string {
        foreach ($template->replacers() as $replacer) {
            $text = $replacer->replace($text, $mailable, $template);
        }

        return $text;
    }

    protected function setSubject(TransactionalMailTemplate $template, array $replacements): void
    {
        $subject = $template->subject ?? '';

        foreach ($replacements as $search => $replace) {
            $subject = str_replace("::{$search}::", $replace, $subject);
        }

        $this->subject($this->executeReplacers($subject, $template, $this));
    }

    protected static function testInstance(): self
    {
        $instance = new self();
        $template = $instance::getTransactionalMailTemplateClass()::first();
        $instance->subject($instance->executeReplacers($template->subject, $template, $instance));

        return $instance;
    }
}
