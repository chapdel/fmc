<?php

function __mc(string $key, array $replace = [], ?string $locale = null): string
{
    $result = __('mailcoach::mailcoach.'.$key, $replace, $locale);

    return str_replace('mailcoach::mailcoach.', '', $result);
}

function __mc_choice(string $key, int $number, array $replace = [], ?string $locale = null): string
{
    $result = trans_choice('mailcoach::mailcoach.'.$key, $number, $replace, $locale);

    return str_replace('mailcoach::mailcoach.', '', $result);
}

function database_date_format_function(string $column, string $format): string
{
    if (config('database.default') === 'pgsql') {
        $format = match ($format) {
            '%Y-%m-%d %H:%I' => 'YYYY-MM-DD HH24:MI',
            '%Y-%m-%d' => 'YYYY-MM-DD',
        };

        return "TO_CHAR('{$column}', '{$format}')";
    }

    return "DATE_FORMAT('{$column}', '{$format}')";
}
