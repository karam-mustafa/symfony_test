<?php

namespace App\Helpers;

trait StatusCode
{
    static int $SERVER_ERROR = 500;
    static int $BAD_REQUEST_ERROR = 400;
    static int $VALIDATION_ERROR = 422;
    static int $NOT_FOUND_PAGE_ERROR = 422;

}