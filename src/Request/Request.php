<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Core\Request;

use Cleancoders\Core\Exception\BadRequestContentException;
use Cleancoders\Core\Request\Traits\RequestFilter;
use Throwable;

/**
 * Application request
 *
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
abstract class Request
{
    use RequestFilter;

    /**
     * Creates a new application request from given payload.
     *
     * @param array<string, mixed> $payload
     * @throws BadRequestContentException
     */
    public static function createFromPayload(array $payload): RequestBuilderInterface
    {
        $requestValidationResult = static::requestPayloadFilter($payload);
        static::throwMissingFieldsExceptionIfNeeded($requestValidationResult['missing_fields']);
        static::throwUnRequiredFieldsExceptionIfNeeded($requestValidationResult['unauthorized_fields']);

        try {
            static::applyConstraintsOnRequestFields($payload);
        } catch (Throwable $exception) {
            throw new BadRequestContentException([
                'message' => $exception->getMessage(),
            ]);
        }

        return new RequestBuilder($payload);
    }

    /**
     * Throws an error if the request has missing fields.
     *
     * @param array<int|string, string> $missingFields
     */
    protected static function throwMissingFieldsExceptionIfNeeded(array $missingFields): void
    {
        if (count($missingFields) > 0) {
            throw new BadRequestContentException([
                'message' => 'missing.required.fields',
                'missing_fields' => $missingFields,
            ]);
        }
    }

    /**
     * Throws an error if the request has unauthorized fields.
     *
     * @param array<int|string, string> $unauthorizedFields
     */
    protected static function throwUnRequiredFieldsExceptionIfNeeded(array $unauthorizedFields): void
    {
        if (count($unauthorizedFields) > 0) {
            throw new BadRequestContentException([
                'message' => 'illegal.fields',
                'unrequired_fields' => $unauthorizedFields,
            ]);
        }
    }

    /**
     * Apply constraints on request fields if needed.
     *
     * @param array<string, mixed> $requestData
     */
    protected static function applyConstraintsOnRequestFields(array $requestData): void
    {
    }
}
