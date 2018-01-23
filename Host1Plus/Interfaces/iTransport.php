<?php

namespace Host1Plus\Interfaces;

use \GuzzleHttp\ClientInterface;

interface iTransport
{
    public function __construct(string $url, string $apiKey, ClientInterface $httpClient = null);
    public function Request(string $method, string $path, array $queryParams = [], $body = null, array $headers = []);
}