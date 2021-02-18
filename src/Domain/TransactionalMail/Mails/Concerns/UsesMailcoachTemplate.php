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
        ray('in template');
        /** @var TransactionalMailTemplate $template */
        $template = TransactionalMailTemplate::firstWhere('name', $name);

        if (! $template) {
            throw CouldNotFindTemplate::make($name, $this);
        }

        $this->subject($template->subject);

        if ($template->from) {
            $this->from($template->from);
        }

        foreach ($template->to as $to) {
            $this->to($to);
        }

        foreach ($template->cc as $cc) {
            $this->cc($cc);
        }

        foreach ($template->bcc as $bcc) {
            $this->bcc($bcc);
        }

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
