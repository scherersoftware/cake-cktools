<?php
namespace CkTools\Lib;

class ApiReturnCode
{
    const SUCCESS = 'success';
    const NOT_AUTHENTICATED = 'not_authenticated';
    const INVALID_PARAMS = 'invalid_params';
    const INVALID_CREDENTIALS = 'invalid_credentials';

    /**
     * Maps return codes to HTTP Status Codes
     *
     * @return array
     */
    public static function getStatusCodeMapping()
    {
        return [
            self::SUCCESS => 200,
            self::NOT_AUTHENTICATED => 403,
            self::INVALID_CREDENTIALS => 401,
            self::INVALID_PARAMS => 400
        ];
    }
}
