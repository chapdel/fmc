<?php

namespace Spatie\Mailcoach\Rules;

use DOMDocument;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class HtmlRule implements Rule
{
    public function passes($attribute, $value)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        try {
            $dom->loadHTML($value, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }


    public function message()
    {
        return 'This HTML is not valid.';
    }
}
