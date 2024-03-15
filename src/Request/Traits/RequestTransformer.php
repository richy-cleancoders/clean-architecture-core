<?php

declare(strict_types=1);

namespace Cleancoders\Core\Request\Traits;

trait RequestTransformer
{
    /**
     * @param array<string, mixed> $requestPayload
     */
    protected static function requestToObject(array $requestPayload): object
    {
        return json_decode(strval(json_encode($requestPayload)));
    }
}
