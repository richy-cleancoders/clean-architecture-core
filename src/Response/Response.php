<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Core\Response;

/**
 * Represent the application response
 *
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
class Response implements ResponseInterface
{
    /**
     * @param array<string, mixed> $data
     */
    private function __construct(
        private readonly bool $success,
        private readonly int $statusCode,
        private readonly ?string $message,
        private readonly array $data
    ) {
    }

    /**
     * Create a new application response
     *
     * @param bool $success True if the response was successfully, false otherwise
     * @param int $statusCode The response status code
     * @param string|null $message The custom response message
     * @param array<string, mixed> $data The response data
     */
    public static function create(
        bool $success = true,
        int $statusCode = StatusCode::NO_CONTENT->value,
        ?string $message = null,
        array $data = []
    ): self {
        return new self(
            success: $success,
            statusCode: $statusCode,
            message: $message,
            data: $data
        );
    }

    /**
     * Check if response is success
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Get response status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get custom response message
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Get the response data
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get specific field from response
     */
    public function get(string $key): mixed
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return null;
    }
}
