<?php

namespace Spatie\Mailcoach\Support;

use Exception;
use GuzzleHttp\Client;

class HttpClient
{
    public function getJson(string $url): array
    {
        try {
            $response = (new Client([
                'headers' => [
                    'User-Agent' => 'Mailcoach',
                    'Accept-Encoding' => 'gzip, deflate',
                ],
                'connect_timeout' => 2,
            ]))
                ->get($url)
                ->getBody();
        } catch (Exception $exception) {
            report($exception);

            return [];
        }

        return json_decode($response, true) ?? [];
    }
}
