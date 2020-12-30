<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns;

use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\CouldNotFindTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

/** @mixin \Illuminate\Mail\Mailable */
trait UsesMailcoachTemplate
{
    use StoresMail;

    public function template(string $name): self
    {
        /** @var TransactionalMailTemplate $template */
        $template = TransactionalMailTemplate::firstWhere('name', $name);

        if (! $template) {
            throw CouldNotFindTemplate::make($name, $this);
        }

        $this->subject($template->subject);

        $this->from($template->from);
        $this->to($template->to);
        $this->cc($template->cc);
        $this->bcc($template->bcc);

        $content = $template->render($this);

        $this->view('mailcoach::mails.transactionalMails.template', compact('content'));

        if ($template->track_opens) {
            $this->trackOpens();
        }

        if ($template->track_clicks) {
            $this->trackClicks();
        }

        return $this;
    }

    protected static function testInstance(): self
    {
        return new self();
    }
}
