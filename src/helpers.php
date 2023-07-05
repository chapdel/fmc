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

function database_date_format_function(string $column)
{
    $dateFormat = [
        'default' => [
            'hour' => 'DATE_FORMAT(' . $column . ", \"%Y-%m-%d %H:%I\")",
            'day' => 'DATE_FORMAT(' . $column . ", \"%Y-%m-%d\")",
        ],
        'pgsql' => [
            'hour' => 'TO_CHAR(' . $column . ', \'YYYY-MM-DD HH24:MI\')',
            'day' => 'TO_CHAR(' . $column . ', \'YYYY-MM-DD\')',
        ],
    ];

    return $dateFormat[config('database.default') == 'pgsql' ? 'pgsql' : 'default'] ?? $dateFormat['default'];
}
