<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Tests\Core\Usecase;

use Cleancoders\Core\Exception\BadRequestContentException;
use Cleancoders\Core\Presenter\Presenter as BasePresenter;
use Cleancoders\Core\Presenter\PresenterInterface;
use Cleancoders\Core\Request\Request as BaseRequest;
use Cleancoders\Core\Request\RequestInterface;
use Cleancoders\Core\Response\Response;
use Cleancoders\Core\Response\StatusCode;
use Cleancoders\Core\Usecase\Usecase as BaseUsecase;
use Cleancoders\Core\Usecase\UsecaseInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
final class UsecaseTest extends TestCase
{
    private RequestInterface $customRequest;
    private PresenterInterface $customPresenter;

    protected function setUp(): void
    {
        $this->customRequest = new class () extends BaseRequest implements RequestInterface {
            protected static array $requestPossibleFields = [
                'field_1' => null,
                'field_2' => null,
            ];
        };

        $this->customPresenter = new class () extends BasePresenter implements PresenterInterface {
        };
    }

    public function testCanExecuteUsecaseAndReturnEmptyContent(): void
    {
        $usecase = new class () extends BaseUsecase implements UsecaseInterface {
            public function execute(): void
            {
                $this->presenter->present(Response::create());
            }
        };

        $usecase
            ->setRequest($this->customRequest::createFromPayload([
                'field_1' => 'field_value',
                'field_2' => [
                    'boolean' => true,
                    'integer' => 666,
                    'float' => 666.666,
                ],
            ]))
            ->setPresenter($this->customPresenter)
            ->execute();

        $response = $this->customPresenter->getResponse();
        $this->assertNotNull($response);
        $this->assertTrue($response->isSuccess());
        $this->assertNull($response->getMessage());
        $this->assertSame(StatusCode::NO_CONTENT->getValue(), $response->getStatusCode());
    }

    public function testCanExecuteUsecaseWithoutRequest(): void
    {
        $usecase = new class () extends BaseUsecase implements UsecaseInterface {
            public function execute(): void
            {
                $this->presenter->present(Response::create(
                    data: [
                        'request' => $this->request,
                        'request_data' => $this->getRequestData(),
                    ]
                ));
            }
        };

        $usecase
            ->setPresenter($this->customPresenter)
            ->execute();

        $response = $this->customPresenter->getResponse();
        $this->assertNull($response->get('request'));
        $this->assertCount(0, $response->get('request_data'));
        $this->assertTrue($response->isSuccess());
        $this->assertNull($response->getMessage());
        $this->assertSame(StatusCode::NO_CONTENT->getValue(), $response->getStatusCode());
    }

    public function testCanExecuteUsecaseWithoutRequestAndPresenter(): void
    {
        $usecase = new class () extends BaseUsecase implements UsecaseInterface {
            public function execute(): void
            {
                throw new BadRequestContentException([
                    'message' => 'throw.error',
                ]);
            }
        };

        try {
            $usecase->execute();
        } catch (BadRequestContentException $exception) {
            $this->assertSame('throw.error', $exception->getMessage());
        }
    }

    public function testCanExecuteUsecaseAndReturnWithContent(): void
    {
        $usecase = new class () extends BaseUsecase implements UsecaseInterface {
            public function execute(): void
            {
                $this->presenter->present(Response::create(
                    statusCode: StatusCode::OK->getValue(),
                    data: $this->getRequestData()
                ));
            }
        };

        $usecase
            ->setRequest($this->customRequest::createFromPayload([
                'field_1' => 'field_default_value',
                'field_2' => [
                    'boolean' => false,
                    'integer' => 666,
                    'float' => 666.666,
                ],
            ]))
            ->setPresenter($this->customPresenter)
            ->execute();

        $response = $this->customPresenter->getResponse();
        $this->assertNotNull($response);
        $this->assertTrue($response->isSuccess());
        $this->assertNull($response->getMessage());
        $this->assertSame(StatusCode::OK->getValue(), $response->getStatusCode());

        $responseData = $response->getData();

        $this->assertArrayHasKey('field_2', $responseData);
        $this->assertSame('field_default_value', $responseData['field_1']);

        $this->assertArrayHasKey('boolean', $responseData['field_2']);
        $this->assertFalse($responseData['field_2']['boolean']);

        $this->assertArrayHasKey('integer', $responseData['field_2']);
        $this->assertSame(666, $responseData['field_2']['integer']);

        $this->assertArrayHasKey('float', $responseData['field_2']);
        $this->assertSame(666.666, $responseData['field_2']['float']);
    }
}
