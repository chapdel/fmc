<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\ViewModels\ViewModel;

class AutomationMailSummaryViewModel extends ViewModel
{
    protected AutomationMail $mail;

    protected Collection $stats;

    protected int $limit;

    public int $failedSendsCount = 0;

    public function __construct(AutomationMail $mail)
    {
        $this->mail = $mail;

        $this->stats = $this->createStats();

        $this->limit = (ceil(max($this->stats->max('opens'), $this->stats->max('clicks')) * 1.1 / 10) * 10) ?: 1;

        $this->failedSendsCount = $this->mail()->sends()->failed()->count();
    }

    public function mail(): AutomationMail
    {
        return $this->mail;
    }

    public function stats(): Collection
    {
        return $this->stats;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    protected function createStats(): Collection
    {
        $start = $this->mail->created_at->toImmutable();

        if ($this->mail->opens()->count() > 0 && $this->mail->opens()->first()->created_at < $start) {
            $start = $this->mail->opens()->first()->created_at->toImmutable();
        }

        return Collection::times(24)->map(function (int $number) use ($start) {
            $datetime = $start->addHours($number - 1);

            return [
                'label' => $datetime->format('H:i'),
                'opens' => $this->mail->opens()->whereBetween('mailcoach_automation_mail_opens.created_at', [$datetime, $datetime->addHour()])->count(),
                'clicks' => $this->mail->clicks()->whereBetween('mailcoach_automation_mail_clicks.created_at', [$datetime, $datetime->addHour()])->count(),
            ];
        });
    }
}
