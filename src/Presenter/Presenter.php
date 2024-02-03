<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Core\Presenter;

use Cleancoders\Core\Response\ResponseInterface;

/**
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
abstract class Presenter
{
    /**
     * The application response
     */
    protected ResponseInterface $response;

    /**
     * Present use case response to the client
     */
    public function present(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    /**
     * Get the use case response
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
