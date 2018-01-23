<?php

namespace Host1Plus\Exception;

class InvalidRequestBody extends \Exception
{
    private $body;

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null, $body = null)
    {
        $this->body = $body;
        parent::__construct($message, $code, $previous);
    }

    public function getBody()
    {
        return $this->body;
    }
}