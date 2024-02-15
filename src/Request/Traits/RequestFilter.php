<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Core\Request\Traits;

/**
 * Application request filter
 *
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
trait RequestFilter
{
    /**
     * All possible request fields. Set field value to null by default.
     * Even if field is not required, you have to register into fields list.
     * This will allow you to control all fields in the request and avoid having unexpected fields.
     *
     * @var array<string, mixed>
     */
    protected static array $requestPossibleFields = [];

    /**
     * Filter request data to identified missing/unauthorized fields.
     *
     * @param array<string, mixed> $requestPayload
     * @return array<string, array<int|string, string>>
     */
    protected static function requestPayloadFilter(array $requestPayload): array
    {
        return [
            'unauthorized_fields' => static::findUnAuthorizedFields(
                $requestPayload,
                static::$requestPossibleFields
            ),
            'missing_fields' => static::findMissingFields(
                static::$requestPossibleFields,
                $requestPayload
            ),
        ];
    }

    /**
     * Find unauthorized fields from request.
     *
     * @param array<string, mixed> $requestPayload
     * @param array<string, mixed> $authorizedFields
     * @return array<int, string>
     */
    protected static function findUnAuthorizedFields(
        array $requestPayload,
        array $authorizedFields,
        string $prefix = ''
    ): array {
        $unAuthorizedFields = [];
        foreach ($requestPayload as $field => $value) {
            $fullKey = $prefix . $field;
            if (!array_key_exists($field, $authorizedFields)) {
                $unAuthorizedFields[] = $fullKey;
            } elseif (is_array($value) && is_array($authorizedFields[$field])) {
                $unAuthorizedFields = array_merge(
                    $unAuthorizedFields,
                    static::findUnauthorizedFields($value, $authorizedFields[$field], $fullKey . '.')
                );
            }
        }

        return $unAuthorizedFields;
    }

    /**
     * Find missing fields from request.
     *
     * @param array<string, mixed> $authorizedFields
     * @param array<string, mixed> $requestPayload
     * @return array<int|string, string>
     */
    protected static function findMissingFields(
        array $authorizedFields,
        array $requestPayload,
        string $prefix = ''
    ): array {
        $missingFields = [];
        foreach ($authorizedFields as $field => $value) {
            $fullKey = $prefix . $field;
            if ($value && !array_key_exists($field, $requestPayload)) {
                $missingFields[$fullKey] = is_array($value) ? 'required field type not matching array' : 'required';
            } elseif (array_key_exists($field, $requestPayload)) {
                if (is_array($value) && gettype($requestPayload[$field]) !== gettype($value)) {
                    $missingFields[$fullKey] = 'required field type not matching array';
                } elseif (is_array($requestPayload[$field]) && is_array($value)) {
                    $missingFields = array_merge(
                        $missingFields,
                        static::findMissingFields($value, $requestPayload[$field], $fullKey . '.')
                    );
                }
            }
        }

        return $missingFields;
    }
}
