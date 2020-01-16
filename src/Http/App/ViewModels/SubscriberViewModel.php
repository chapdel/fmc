<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Spatie\Mailcoach\Models\Subscriber;
use Spatie\ViewModels\ViewModel;

class SubscriberViewModel extends ViewModel
{
    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber = null)
    {
        $this->subscriber = $subscriber ?? new Subscriber();
    }

    public function totalSendsCount(): int
    {
        return $this->subscriber->sends()->count();
    }
}
