<?php

/*
 * This file is part of the Cleancoders Core package.
 *
 * (c) Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
 */

declare(strict_types=1);

namespace Cleancoders\Tests\Core\Response;

use Cleancoders\Core\Response\Response;
use Cleancoders\Core\Response\StatusCode;
use PHPUnit\Framework\TestCase;

/**
 * @author Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com
 */
final class ResponseTest extends TestCase
{
    public function testCanCreateResponseWithContent(): void
    {
        $response = Response::create();

        $this->assertTrue($response->isSuccess());
        $this->assertNull($response->getMessage());
        $this->assertEquals(StatusCode::NO_CONTENT->getValue(), $response->getStatusCode());
        $this->assertCount(0, $response->getData());
    }

    public function testCanCreateNewSuccessResponseWithOkStatusCode(): void
    {
        $responseData = [
            'field' => 1,
        ];
        $response = Response::create(
            statusCode: StatusCode::OK->getValue(),
            message: 'Successfully get resource.',
            data: $responseData
        );

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(StatusCode::OK->getValue(), $response->getStatusCode());
        $this->assertEquals('Successfully get resource.', $response->getMessage());
        $this->assertEquals($responseData, $response->getData());
    }

    public function testCanCreateNewSuccessResponseWithCreatedStatusCode(): void
    {
        $responseData = [
            'field_1' => 1,
            'field_2' => 'value',
        ];
        $response = Response::create(
            statusCode: StatusCode::CREATED->getValue(),
            message: 'Successfully create resource.',
            data: $responseData
        );

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(StatusCode::CREATED->getValue(), $response->getStatusCode());
        $this->assertEquals('Successfully create resource.', $response->getMessage());
        $this->assertEquals($responseData, $response->getData());
    }

    public function testCanCreateNewFailedResponseWithBadStatusCode(): void
    {
        $response = Response::create(
            success: false,
            statusCode: StatusCode::BAD_REQUEST->getValue(),
            data: []
        );

        $this->assertFalse($response->isSuccess());
        $this->assertNull($response->getMessage());
        $this->assertEquals(StatusCode::BAD_REQUEST->getValue(), $response->getStatusCode());
    }

    public function testCanCreateNewResponseAndGetExistingDataByKey(): void
    {
        $response = Response::create(
            success: false,
            statusCode: StatusCode::BAD_REQUEST->getValue(),
            data: [
                'key_1' => 1234,
            ]
        );

        $this->assertNotNull($response->get('key_1'));
    }

    public function testCanCreateNewResponseAndGetNotExistingDataByKey(): void
    {
        $response = Response::create(
            success: false,
            statusCode: StatusCode::BAD_REQUEST->getValue(),
            data: [
                'key_1' => 1234,
            ]
        );

        $this->assertNull($response->get('key_2'));
    }
}
