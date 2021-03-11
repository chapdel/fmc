<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Exception;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\Factory;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

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
            'markdown' => Str::of($template->body)->markdown(),
            'blade-markdown' => Str::of($this->compileBlade($template->body, $mailable->buildViewData()))->markdown(),

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

    protected function compileBlade(string $bladeString, array $arguments): string
    {
        $arguments['__env'] = app(Factory::class);

        $generated = Blade::compileString($bladeString);

        ob_start() and extract($arguments, EXTR_SKIP);

        // We'll include the view contents for parsing within a catcher
        // so we can avoid any WSOD errors. If an exception occurs we
        // will throw it out to the exception handler.
        try {
            eval('?>'.$generated);
        }
        // If we caught an exception, we'll silently flush the output
        // buffer so that no partially rendered views get thrown out
        // to the client and confuse the user with junk.
        catch (Exception $exception) {
            ob_get_clean();

            throw $exception;
        }

        $content = ob_get_clean();

        return $content;
    }
}
