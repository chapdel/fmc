<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Blade;
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
        return match ($template->type) {
            'blade' => Blade::render($template->body, $mailable->buildViewData()),
            'markdown' => Markdown::parse($template->body),
            'blade-markdown' => $this->compileBladeMarkdown(
                bladeString: $template->body,
                data: $mailable->buildViewData(),
                theme: $mailable->theme
            ),

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

    protected function compileBladeMarkdown(string $bladeString, array $data, string $theme = null): string
    {
        $tempDir = (new TemporaryDirectory())->create();
        $path = $tempDir->path('temporary-template-view.blade.php');

        File::put($path, $bladeString);

        $viewFactory = app(Factory::class);
        $viewFactory->addLocation($tempDir->path());

        $html = app(Markdown::class)
            ->theme($theme ?? 'default')
            ->render('temporary-template-view', $data)
            ->toHtml();

        $tempDir->delete();

        return $html;
    }
}
