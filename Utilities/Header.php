<?php

namespace Utilities;

final class Header
{
    // Numeric constants of header codes
    const OK                  = 200;
    const Created             = 201;
    const NoContent           = 204;
    const BadRequest          = 400;
    const Unauthorized        = 401;
    const Forbidden           = 403;
    const NotFound            = 404;
    const MethodNotAllowed    = 405;
    const Conflict            = 409;
    const InternalServerError = 500;
    const NotImplemented      = 501;

    // String constnats of header codes
    const OKStr                  = 'OK';
    const CreatedStr             = 'Created';
    const NoContentStr           = 'No Content';
    const BadRequestStr          = 'Bad Request';
    const UnauthorizedStr        = 'Unauthorized';
    const ForbiddenStr           = 'Forbidden';
    const NotFoundStr            = 'Not Found';
    const MethodNotAllowedStr    = 'Method Not Allowed';
    const ConflictStr            = 'Conflict';
    const InternalServerErrorStr = 'Internal Server Error';
    const NotImplementedStr      = 'Not Implemented';

    /**
     * Set authentication realm
     *
     * @param string $realm
     */
    public static function setAuth($realm = '')
    {
        \header("WWW-Authenticate: Basic realm=\"{$realm}\"");
    }

    /**
     * Set content type
     *
     * @param string $contentType
     */
    public static function setContentType($contentType)
    {
        \header("Content-type: {$contentType}");
    }

    /**
     * Set file transfer headers
     *
     * @param string $filename
     */
    public static function setFileTransfer($filename)
    {
        \header('Content-Description: File Transfer');
        \header('Content-Type: application/octet-stream');
        \header("Content-Disposition: attachment; filename={$filename}");
        \header('Expires: 0');
        \header('Cache-Control: must-revalidate');
        \header('Pragma: public');
    }

    /**
     *
     * @param mixed $length
     */
    public static function setContentLength($length)
    {
        \header("Content-Length: {$length}");
    }

    /**
     *
     * @param integer $code
     */
    public static function setByCode($code)
    {
        switch ($code)
        {
            case self::OK:
                self::setOK();
                break;
            case self::Created:
                self::setCreated();
                break;
            case self::NoContent:
                self::setNoContent();
                break;
            case self::BadRequest:
                self::setBadRequest();
                break;
            case self::Unauthorized:
                self::setUnauthorized();
                break;
            case self::Forbidden:
                self::setForbidden();
                break;
            case self::NotFound:
                self::setNotFound();
                break;
            case self::Conflict:
                self::setConflict();
                break;
            case self::MethodNotAllowed:
                self::setMethodNotAllowed();
                break;
            case self::InternalServerError:
                self::setInternalServerError();
                break;
            case self::NotImplemented:
                self::setNotImplemented();
                break;
            default :
                self::setInternalServerError();
        }
    }

    // 2xx - Success
    public static function setOK()
    {
        self::_setProtocolHeader(self::OK, self::OKStr);
    }

    public static function setCreated()
    {
        self::_setProtocolHeader(self::Created, self::CreatedStr);
    }

    public static function setNoContent()
    {
        self::_setProtocolHeader(self::NoContent, self::NoContentStr);
    }

    // 4xx - Client Errors
    public static function setBadRequest()
    {
        self::_setProtocolHeader(self::BadRequest, self::BadRequestStr);
    }

    public static function setUnauthorized()
    {
        self::_setProtocolHeader(self::Unauthorized, self::UnauthorizedStr);
    }

    public static function setForbidden()
    {
        self::_setProtocolHeader(self::Forbidden, self::ForbiddenStr);
    }

    public static function setNotFound()
    {
        self::_setProtocolHeader(self::NotFound, self::NotFoundStr);
    }

    public static function setConflict()
    {
        self::_setProtocolHeader(self::Conflict, self::ConflictStr);
    }

    public static function setMethodNotAllowed()
    {
        self::_setProtocolHeader(self::MethodNotAllowed, self::MethodNotAllowedStr);
    }

    // 5xx - Server Errors
    public static function setInternalServerError()
    {
        self::_setProtocolHeader(self::InternalServerError, self::InternalServerErrorStr);
    }

    public static function setNotImplemented()
    {
        self::_setProtocolHeader(self::NotImplemented, self::NotImplementedStr);
    }


    public static function getRequestHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value)
        {
            if (\substr($key, 0, 5) <> 'HTTP_')
                continue;

            $headers[ \str_replace(' ', '-', \ucwords(\str_replace('_', ' ', \strtolower(\substr($key, 5))))) ] = $value;
        }

        return $headers;
    }

    // Private stuff
    private static function _setProtocolHeader($code, $description)
    {
        \header("{$_SERVER['SERVER_PROTOCOL']} {$code} {$description}");
    }
}