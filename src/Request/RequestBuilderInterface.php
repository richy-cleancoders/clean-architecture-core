<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Core\Request;

/**
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
interface RequestBuilderInterface
{
    /**
     * Get application request data as object.
     */
    public function getRequestDataAsObject(): object;

    /**
     * Get application request data as array.
     *
     * @return array<string, mixed>
     */
    public function getRequestDataAsArray(): array;

    /**
     * Get application request uniq id.
     */
    public function getRequestId(): string;
}
