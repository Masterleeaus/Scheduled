<?php
declare(strict_types=1);

namespace App\Central\Http;

/** The only way Central code reaches the network. Tests inject a fake; production uses CurlHttpTransport. */
interface HttpTransport
{
    /**
     * @param array<string,string> $fields
     * @param list<string> $headers
     * @return array{status:int, json:?array<mixed>}
     */
    public function postForm(string $url, array $fields, array $headers = []): array;

    /**
     * @param list<string> $headers
     * @return array{status:int, json:?array<mixed>}
     */
    public function getJson(string $url, array $headers = []): array;
}
