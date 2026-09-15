<?php

declare(strict_types=1);

namespace Ugarit\Boost\Concerns;

use Heritage\Http\Client\PendingRequest;
use Heritage\Http\Client\Response;
use Heritage\Support\Facades\Http;

trait MakesHttpRequests
{
    public function client(): PendingRequest
    {
        $client = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:140.0) Gecko/20100101 Firefox/140.0 Ugarit Boost',
        ]);

        // Disable SSL verification for local development URLs and testing
        if (app()->environment(['local', 'testing']) || str_contains((string) config('boost.hosted.api_url', ''), '.test')) {
            return $client->withoutVerifying();
        }

        return $client;
    }

    public function get(string $url): Response
    {
        return $this->client()->get($url);
    }

    /**
     * @param  array<string, mixed>  $json
     */
    public function json(string $url, array $json): Response
    {
        return $this->client()->asJson()->post($url, $json);
    }
}
