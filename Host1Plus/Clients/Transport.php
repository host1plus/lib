<?php

namespace Host1Plus\Clients;

use \GuzzleHttp\Client as HttpClient;
use \GuzzleHttp\ClientInterface;
use \GuzzleHttp\Psr7\Request;

// interfaces
use \Host1Plus\Interfaces\iTransport;

// enums
use \Host1Plus\Enums\Errors;

// utilities
use \Utilities\Header;

// exceptions
use \GuzzleHttp\Exception\BadResponseException;
use \Host1Plus\Exception\{InvalidResponseBody, BadRequest, Unauthorized, NotFound, InternalServerError, NotImplemented};

/**
 * @todo comments
 */
class Transport implements iTransport
{
    private $httpClient;
    private $url;
    private $apiKey;

    /**
     *
     * @param string $url
     * @param string $apiKey
     * @param HttpClient $httpClient
     */
    public function __construct(string $url, string $apiKey, ClientInterface $httpClient = null)
    {
        $this->url        = \rtrim($url, '/');
        $this->apiKey     = $apiKey;
        $this->httpClient = (\is_null($httpClient)) ? new HttpClient() : $httpClient;
    }

    /**
     * @todo improve InvalidResponseBody exception throw, needs more readability for higher level implementations
     *
     * @param string $method
     * @param string $path
     * @param array $queryParams
     * @param type $body
     * @param array $headers
     * @return type
     * @throws InvalidResponseBody
     * @throws BadRequest
     * @throws Unauthorized
     * @throws NotFound
     * @throws NotImplemented
     * @throws InternalServerError
     */
    public function Request(string $method, string $path, array $queryParams = [], $body = null, array $headers = [])
    {
        $uri = "{$this->url}/{$path}";
        if (\count($queryParams) > 0)
            $uri .= '?' . \http_build_query($queryParams);

        $headers['X-Auth-Token'] = $this->apiKey;

        $request = new Request($method, $uri, $headers, $body);
        try
        {
            $response = $this->httpClient->send($request);
            if ($response->getStatusCode() == Header::NoContent)
                return;

            try
            {
                $responseBody = \GuzzleHttp\json_decode($response->getBody(), true);
                if (!\is_array($responseBody) || !isset($responseBody['data']) || !\is_array($responseBody['data']))
                    throw new InvalidResponseBody(Errors::InvalidResponseBody, 0, null, $response);
            }
            catch (\InvalidArgumentException $ex)
            {
                throw new InvalidResponseBody(Errors::InvalidResponseBody, 0, $ex, $response);
            }

            return $responseBody['data'];
        }
        catch (BadResponseException $ex)
        {
            try
            {
                $responseBody = \GuzzleHttp\json_decode($ex->getResponse()->getBody(), true);
                if (!\is_array($responseBody) || !isset($responseBody['message']) || !\is_string($responseBody['message']))
                    throw new InvalidResponseBody(Errors::InvalidResponseBody, 0, $ex, $ex->getResponse());
            }
            catch (\InvalidArgumentException $iaEx)
            {
                throw new InvalidResponseBody(Errors::InvalidResponseBody, 0, $iaEx, $ex->getResponse());
            }

            $statusCode = $ex->getResponse()->getStatusCode();
            $message    = \sprintf(Errors::BadResponse, $method, $path, $responseBody['message']);
            switch ($statusCode)
            {
                case Header::BadRequest:
                    throw new BadRequest($message, Header::BadRequest, $ex, $responseBody);
                case Header::Unauthorized:
                    throw new Unauthorized($message, Header::Unauthorized, $ex);
                case Header::NotFound:
                    throw new NotFound($message, Header::NotFound, $ex);
                case Header::NotImplemented:
                    throw new NotImplemented($message, Header::NotImplemented, $ex);
                default:
                    throw new InternalServerError($message, Header::InternalServerError, $ex);
            }
        }
    }
}