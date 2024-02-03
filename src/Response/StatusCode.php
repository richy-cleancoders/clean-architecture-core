<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Core\Response;

/**
 * Response status code list
 *
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
enum StatusCode: int
{
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case RESOURCE_ALREADY_EXISTS = 409;
    case EXPECTATION_FAILED = 417;
    case LOCKED = 423;
    case TOO_MANY_REQUESTS = 429;
    case INTERNAL_SERVER_ERROR = 500;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;

    /**
     * Get status code value
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
