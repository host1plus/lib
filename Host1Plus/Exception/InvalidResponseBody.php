<?php

namespace Host1Plus\Exception;

use \GuzzleHttp\Psr7\Response;

class InvalidResponseBody extends \Exception
{
    /**
     *
     * @var Response
     */
    private $response;

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null, Response $response = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    public function getResponse()
    {
        return $this->response;
    }
}