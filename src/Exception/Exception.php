<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Core\Exception;

use Cleancoders\Core\Response\StatusCode;
use RuntimeException;

/**
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
abstract class Exception extends RuntimeException
{
    /**
     * Exception status code.
     *
     * @var int The status code
     */
    protected int $statusCode = StatusCode::BAD_REQUEST->value;

    /**
     * Custom data into the exception.
     *
     * @var array<string, mixed>
     */
    protected array $errors;

    /**
     * @param array<string, mixed> $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct(
            message: $errors['message'] ?? '',
            code: $this->statusCode
        );
        $this->errors = $errors;
    }

    /**
     * Get custom exception errors.
     *
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get exception errors for logs.
     *
     * @return array<string, mixed>
     */
    public function getErrorsForLog(): array
    {
        return [
            'errors' => $this->errors,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'previous' => $this->getPrevious(),
            'trace_as_array' => $this->getTrace(),
            'trace_as_string' => $this->getTraceAsString(),
        ];
    }
}
