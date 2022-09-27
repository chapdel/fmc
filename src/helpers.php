<?php

function __mc(string $key, array $replace = [], ?string $locale = null): string
{
    $result = __('mailcoach::mailcoach.' . $key, $replace, $locale);

    return str_replace('mailcoach::mailcoach.', '', $result);
}
