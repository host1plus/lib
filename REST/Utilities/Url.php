<?php

namespace REST\Utilities;

final class Url
{
    public static function parseFromServer()
    {
        $php_self = \explode('/', $_SERVER['PHP_SELF']);
        \array_pop($php_self);

        return \substr_replace( \rtrim( \html_entity_decode($_SERVER['REQUEST_URI']), '/' ) , '', 0, \strlen( \implode('/', $php_self) ));
    }
}