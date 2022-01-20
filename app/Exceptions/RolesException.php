<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;

class RolesException extends AuthenticationException
{
    /**
     * Roles Error
     *
     * @param string|null     $message  [description]
     * @param \Throwable|null $previous [description]
     * @param array           $headers  [description]
     * @param int|integer     $code     [description]
     */
    public function sss__construct(string $error = '')
    {

    }
}
