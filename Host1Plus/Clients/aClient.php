<?php

namespace Host1Plus\Clients;

// enums
use Host1Plus\Enums\Errors;

// exceptions
use Host1Plus\Exception\InvalidRequestBody;

abstract class aClient
{
    protected function jsonEncodeBody(array $params)
    {
        try
        {
            return \GuzzleHttp\json_encode($params);
        }
        catch (\InvalidArgumentException $ex)
        {
            throw new InvalidRequestBody('', 0, $ex, $params);
        }
    }

    protected function isValidServerId(int $serverId)
    {
        if ($serverId <= 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'serverId', 'integer above 0', $serverId));

        return true;
    }
}