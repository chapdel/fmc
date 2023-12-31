<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;
use Spatie\Navigation\Section;

class BootstrapNavigation
{
    use UsesMailcoachModels;

    public function handle(Request $request, $next)
    {
        $navigation = app(MainNavigation::class);

        $navigation->addIf($request->user()?->can('viewMailcoach'), __mc('Dashboard'), route('mailcoach.dashboard'))
            ->addIf($request->user()?->can('viewAny', self::getCampaignClass()), __mc('Campaigns'), route('mailcoach.campaigns'))
            ->addIf($request->user()?->can('viewAny', self::getAutomationClass()), __mc('Automations'), route('mailcoach.automations'), function (Section $section) {
                $section
                    ->add(__mc('Automations'), route('mailcoach.automations'))
                    ->add(__mc('Emails'), route('mailcoach.automations.mails'));
            })
            ->addIf($request->user()?->can('viewAny', self::getEmailListClass()), __mc('Lists'), route('mailcoach.emailLists'))
            ->addIf($request->user()?->can('viewAny', self::getTransactionalMailLogItemClass()), __mc('Transactional'), route('mailcoach.transactionalMails'), function (Section $section) {
                $section
                    ->add(__mc('Log'), route('mailcoach.transactionalMails'))
                    ->add(__mc('Emails'), route('mailcoach.transactionalMails.templates'));
            })
            ->addIf($request->user()?->can('viewAny', self::getTemplateClass()), __mc('Templates'), route('mailcoach.templates'));

        foreach (Mailcoach::$mainMenuItems as $item) {
            $navigation->add($item->label, $item->url, function (Section $section) use ($item) {
                if (! $item->children) {
                    return null;
                }

                foreach ($item->children as $child) {
                    $section->add($child->label, $child->url);
                }
            });
        }

        return $next($request);
    }
}
