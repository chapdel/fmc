<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\PersonalizedReplacer as AutomationPersonalizedReplacer;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer as CampaignPersonalizedReplacer;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Mailcoach;

class PersonalizeTextAction
{
    protected RenderTwigAction $renderTwigAction;

    public function __construct(
        protected GetReplaceContextForSendAction $getReplaceContextForSendAction,
    ) {
        $this->renderTwigAction = Mailcoach::getSharedActionClass('render_twig', RenderTwigAction::class);
    }

    public function execute(?string $text, Send $pendingSend): string
    {
        $text ??= '';

        /** @var Subscriber $subscriber */
        $subscriber = $pendingSend->subscriber;

        $text = str_ireplace('::sendUuid::', $pendingSend->uuid, $text);
        $text = str_ireplace('::subscriber.uuid::', $subscriber->uuid, $text);

        $context = $this->getReplaceContextForSendAction->execute($pendingSend);

        if ($sendable = $pendingSend->getSendable()) {
            foreach ($sendable->getReplacers() as $replacer) {
                if (method_exists($replacer, 'context')) {
                    $context = array_merge($context, $replacer->context());
                }
            }
        }

        $text = $this->renderTwigAction->execute($text, $context);

        if (! $sendable) {
            return $text;
        }

        return $sendable->getReplacers()
            ->reduce(fn (string $text, $replacer) => match (true) {
                $replacer instanceof CampaignPersonalizedReplacer => $replacer->replace($text, $pendingSend),
                $replacer instanceof AutomationPersonalizedReplacer => $replacer->replace($text, $pendingSend),
                $replacer instanceof CampaignReplacer => $replacer->replace($text, $pendingSend->campaign),
                $replacer instanceof AutomationMailReplacer => $replacer->replace($text, $pendingSend->automationMail),
            }, $text);
    }
}
