<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
use Spatie\ViewModels\ViewModel;

class SubscriberViewModel extends ViewModel
{
    use UsesMailcoachModels;

    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber = null)
    {
        $class = $this->getSubscriberClass();
        $this->subscriber = $subscriber ?? new $class();
    }

    public function totalSendsCount(): int
    {
        return $this->subscriber->sends()->count();
    }
}
