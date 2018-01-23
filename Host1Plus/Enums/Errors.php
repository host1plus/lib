<?php

namespace Host1Plus\Enums;

final class Errors
{
    const BadResponse         = 'failed to execute API operation %s - %s : %s';
    const InvalidResponseBody = 'unable to parse response body';
    const NotSetParameter     = 'parameter \'%s\' not set';
    const NotProvidedParam    = "parameter '%s' not provided";
    const InvalidParameter    = 'parameter \'%s\' invalid, expected: %s, got: %s';
    const Action              = 'failed to execute %s action, caught exception \'%s\' : %s';
    const CreateFile          = 'failed to create file \'%s\', path: %s';
    const ReadFile            = 'unable to read file contents, path: %s';
    const WriteFile           = 'unable to write file contents, path: %s';
    const DecodeJson          = 'unable to decode %s : %s';
    const EncodeJson          = 'unable to encode %s : %s';
    const FindItem            = 'failed to find \'%s\'';
    const FindItemExt         = 'failed to find %s, caught exception \'%s\' : %s';
    const CacheInit           = 'failed to initialize and validate cache directory: %s';
    const CacheAction         = 'failed to %s %s cache contents: %s';
    const CountOjbect         = 'invalid %s count, expected: %s, got: %s';
    const UpdateObject        = 'failed to update %s';
    const NotFound            = "%s not found, using %s = '%s'";
    const NotFoundSimple      = "%s not found";
    const InvalidState        = "%s is not in valid state, expected: [%s], got: %s";
    const NotConfigured       = "%s is not not configured: %s";
    const NotImplemented      = "%s is not implemented: %s";
    const ParamsRequired      = "at least %s parameter(s) required";
    const Limit               = "%s limit of %s has been reached";
}