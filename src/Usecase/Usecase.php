<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Core\Usecase;

use Cleancoders\Core\Presenter\PresenterInterface;
use Cleancoders\Core\Request\RequestBuilderInterface;

/**
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
abstract class Usecase
{
    /**
     * The application request to be processed by usecase
     */
    protected ?RequestBuilderInterface $request = null;

    /**
     * The presenter to which the usecase processing response will be sent
     */
    protected ?PresenterInterface $presenter = null;

    /**
     * Set applicative request to be processed by usecase
     */
    public function setRequest(?RequestBuilderInterface $request = null): static
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set presenter to get usecase response
     */
    public function setPresenter(?PresenterInterface $presenter = null): static
    {
        $this->presenter = $presenter;

        return $this;
    }

    /**
     * Get request data
     *
     * @return array<string, mixed>|object
     */
    protected function getRequestData(bool $asArray = true): array|object
    {
        if ($this->request === null) {
            return [];
        }

        if ($asArray) {
            return $this->request->getRequestDataAsArray();
        }

        return $this->request->getRequestDataAsObject();
    }
}
