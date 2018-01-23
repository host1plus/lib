<?php

namespace Host1Plus\Utilities;

use \Host1Plus\Enums\Errors;

final class Cache
{
    public static function InitDir($filename)
    {
        if (\file_exists($filename))
        {
            if (!\is_dir($filename))
                throw new \InvalidArgumentException( \sprintf(Errors::InvalidParameter, $filename, 'dir', \filetype($filename)) );
        }
        elseif (!\mkdir($filename, 0755))
            throw new \InvalidArgumentException( \sprintf(Errors::CreateFile, 'dir', $filename) );
    }

    public static function GetContents($filename)
    {
        if (\file_exists($filename))
        {
            if (!\is_file($filename))
                throw new \InvalidArgumentException( \sprintf(Errors::InvalidParameter, $filename, 'file', \filetype($filename)), 1 );

            $contents = \file_get_contents($filename);
            if ($contents === false)
                throw new \InvalidArgumentException( \sprintf(Errors::ReadFile, $filename), 2 );

            $data = \json_decode($contents, true);
            if (\json_last_error() !== \JSON_ERROR_NONE)
                throw new \InvalidArgumentException( \sprintf(Errors::DecodeJson, $filename, \json_last_error_msg()), 3 );

            return $data;
        }
        elseif (!\touch($filename))
            throw new \InvalidArgumentException( \sprintf(Errors::CreateFile, 'file', $filename), 4 );

        $data = ['date' => \time(), 'items' => []];
        if (\file_put_contents($filename, \json_encode($data)) === false)
            throw new \InvalidArgumentException( \sprintf(Errors::WriteFile, $filename), 5 );

        return $data;
    }

    public static function PutContents($filename, array $contents)
    {
        if (\file_exists($filename) && !\is_file($filename))
            throw new \InvalidArgumentException( \sprintf(Errors::InvalidParameter, $filename, 'file', \filetype($filename)) );
        elseif (!\touch($filename))
            throw new \InvalidArgumentException( \sprintf(Errors::CreateFile, 'file', $filename) );

        $contents['date'] = \time();
        if (\file_put_contents($filename, \json_encode($contents)) === false)
            throw new \InvalidArgumentException( \sprintf(Errors::WriteFile, $filename) );
    }
}