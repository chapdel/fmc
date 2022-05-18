<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;
use Spatie\Navigation\Section;

class BootstrapNavigation
{
    use UsesMailcoachModels;

    public function handle(Request $request, $next)
    {
        app(MainNavigation::class)
            ->add(__('mailcoach - Dashboard'), route('mailcoach.dashboard'))
            ->addIf($request->user()?->can('viewAny', self::getCampaignClass()), __('mailcoach - Newsletter'), route('mailcoach.campaigns'), function (Section $section) {
                $section
                    ->add(__('mailcoach - Campaigns'), route('mailcoach.campaigns'))
                    ->add(__('mailcoach - Templates'), route('mailcoach.templates'));
            })
            ->addIf($request->user()?->can('viewAny', self::getAutomationClass()), __('mailcoach - Drip'), route('mailcoach.automations'), function (Section $section) {
                $section
                    ->add(__('mailcoach - Automations'), route('mailcoach.automations'))
                    ->add(__('mailcoach - Emails'), route('mailcoach.automations.mails'));
            })
            ->addIf($request->user()?->can('viewAny', self::getEmailListClass()), __('mailcoach - Audience'), route('mailcoach.emailLists'))
            ->addIf($request->user()?->can('viewAny', self::getTransactionalMailClass()), __('mailcoach - Transactional'), route('mailcoach.transactionalMails'), function (Section $section) {
                $section
                    ->add(__('mailcoach - Log'), route('mailcoach.transactionalMails'))
                    ->add(__('mailcoach - Templates'), route('mailcoach.transactionalMails.templates'));
            });

        return $next($request);
    }
}
