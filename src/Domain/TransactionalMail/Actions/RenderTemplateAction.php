<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Illuminate\Container\Container;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class RenderTemplateAction
{
    public function execute(TransactionalMailTemplate $template, Mailable $mailable)
    {
        $body = $this->renderTemplateBody($template, $mailable);

        $body = $this->executeReplacers($body, $template, $mailable);

        return $body;
    }

    protected function renderTemplateBody(TransactionalMailTemplate $template, Mailable $mailable): string
    {
        return match($template->type) {
            'blade' => $this->compileBlade($template->body, $mailable->buildViewData()),
            'markdown' => Markdown::parse($template->body),
            'blade-markdown' => $this->compileBlade($template->body, $mailable->buildViewData(), $mailable->theme),

            default => $template->body,
        };
    }

    protected function executeReplacers(string $body, TransactionalMailTemplate $template, Mailable $mailable): string
    {
        foreach ($template->replacers() as $replacer) {
            $body = $replacer->replace($body, $mailable, $template);
        }

        return $body;
    }

    protected function compileBlade(string $bladeString, array $arguments, string $theme = null): string
    {
        $markdown = Container::getInstance()->make(Markdown::class);

        if ($theme) {
            $markdown->theme($theme);
        }

        $tempDir = new TemporaryDirectory();
        $tempDir->create();
        $path = $tempDir->path('temporary-template-view.blade.php');

        File::put($path, $bladeString);

        app(Factory::class)->addLocation($tempDir->path());

        $html = $markdown->render('temporary-template-view', $arguments)->toHtml();

        $tempDir->delete();

        return $html;
    }
}
