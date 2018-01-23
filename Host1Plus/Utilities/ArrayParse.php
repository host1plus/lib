<?php

namespace Host1Plus\Utilities;

// interfaces / enums
use \Host1Plus\Enums\Errors;

final class ArrayParse
{
    /**
     * Checks if parameter defined by $key is set in $params list and is of valid type (int) and then sets it to $data set
     *
     * @param type $key
     * @param array $params
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public static function IntParam($key, array $params, array &$data)
    {
        if (isset($params[$key]))
        {
            if (!\is_int($params[$key]))
                throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, $key, 'integer', $params[$key]));

            $data[$key] = $params[$key];
        }
    }

    /**
     * Checks if parameter defined by $key is set in $params list and is of valid type (bool) and then sets it to $data set
     *
     * @param type $key
     * @param array $params
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public static function BoolParam($key, array $params, array &$data)
    {
        if (isset($params[$key]))
        {
            if (!\is_bool($params[$key]))
                throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, $key, 'bool', $params[$key]));

            $data[$key] = $params[$key];
        }
    }

    /**
     * Checks if parameter defined by $key is set in $params list and is of valid type (string) and then sets it to $data set
     *
     * @param type $key
     * @param array $params
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public static function StringParam($key, array $params, array &$data)
    {
        if (isset($params[$key]))
        {
            if (!\is_string($params[$key]))
                throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, $key, 'string', $params[$key]));

            $data[$key] = $params[$key];
        }
    }

    public static function ArrayParam($key, array $params, array &$data)
    {
        if (isset($params[$key]))
        {
            if (!\is_array($params[$key]))
                throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, $key, 'array', $params[$key]));

            $data[$key] = $params[$key];
        }
    }

    /**
     * checks if parameter defined by $key is set in $params list, validates its type (int) and sets it to $data list
     *
     * @param type $key
     * @param array $params
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public static function IntParamSet($key, array $params, array &$data)
    {
        if (!isset($params[$key]))
            throw new \InvalidArgumentException( \sprintf(Errors::NotSetParameter, $key) );

        if (!\is_int($params[$key]))
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, $key, 'integer', $params[$key]));

        $data[$key] = $params[$key];
    }

    /**
     * checks if parameter defined by $key is set in $params list, validates its type (bool) and sets it to $data list
     *
     * @param type $key
     * @param array $params
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public static function BoolParamSet($key, array $params, array &$data)
    {
        if (!isset($params[$key]))
            throw new \InvalidArgumentException( \sprintf(Errors::NotSetParameter, $key) );

        if (!\is_bool($params[$key]))
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, $key, 'bool', $params[$key]));

        $data[$key] = $params[$key];
    }

    /**
     * checks if parameter defined by $key is set in $params list, validates its type (string) and sets it to $data list
     *
     * @param type $key
     * @param array $params
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public static function StringParamSet($key, array $params, array &$data)
    {
        if (!isset($params[$key]))
            throw new \InvalidArgumentException( \sprintf(Errors::NotSetParameter, $key) );

        if (!\is_string($params[$key]))
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, $key, 'string', $params[$key]));

        $data[$key] = $params[$key];
    }

    public static function ArrayParamSet($key, array $params, array &$data)
    {
        if (!isset($params[$key]))
            throw new \InvalidArgumentException( \sprintf(Errors::NotSetParameter, $key) );

        if (!\is_array($params[$key]))
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, $key, 'array', $params[$key]));

        $data[$key] = $params[$key];
    }
}