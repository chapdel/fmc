<?php

namespace Spatie\Mailcoach\Rules;

use DOMDocument;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class HtmlRule implements Rule
{
    private ?Exception $exception;

    public function passes($attribute, $value)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        try {
            $value = preg_replace('/&(?!amp;)/', '&amp;', $value);

            $dom->loadHTML($value, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING);

            return true;
        } catch (Exception $exception) {
            $this->exception = $exception;

            return false;
        }
    }

    public function message()
    {
        return __('The HTML is not valid (:message).', ['message' => $this->exception->getMessage()]);
    }
}
