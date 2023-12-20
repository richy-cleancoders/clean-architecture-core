# Core library for clean architecture in PHP

## Introduction

This documentation guides you through the utilization of the core library for implementing clean architecture in PHP.
We'll explore the creation of custom application request and use cases, paying special attention to handling missing and
unauthorized fields.

Practical examples are provided using code snippets from a test suite to showcase the library's usage in building a
modular and clean PHP application.

## Prerequisites

Ensure that you have the following:

- `PHP` installed on your machine (version `8.2.0 or higher`).
- `Composer` installed for dependency management.

## Installation

```
composer require urichy/clean-architecture-core
```

## Core Overview

### Application Request

Requests serve as input object, encapsulating data from your http controller. In the core library, use the `\Cleancoders\Core\Request\Request` class
as the foundation for creating custom application request object. Define the expected fields using the `requestPossibleFields` property.

### Presenter

Presenters handle the output logic of your usecase. You have to extends `\Cleancoders\Core\Presenter\Presenter` and
implements `\Cleancoders\Core\Presenter\PresenterInterface`

### Usecase

Use cases encapsulate business logic and orchestrate the flow of data between requests, entities, and presenters.
Extends the `\Cleancoders\Core\Usecase\Usecase` class and implements `\Cleancoders\Core\Usecase\UsecaseInterface` with the execute method.

### Response

- Use `\Cleancoders\Core\Response\Response` to create usecase `response`.
- Supports success/failure status, custom message, HTTP status codes, and response data.

## Example of how to use the core library

1. Creating a custom request and handling missing/unauthorized fields

- Extends `\Cleancoders\Core\Request\Request` and implements `\Cleancoders\Core\Request\RequestInterface` to create
  custom application request objects.
- Define the possible fields in the `requestPossibleFields` property.

```php
<?php

declare(strict_types=1);

use Cleancoders\Core\Request\Request;
use Cleancoders\Core\Request\RequestInterface;

interface CustomRequestInterface extends RequestInterface
{

}

final class CustomRequest extends Request implements CustomRequestInterface
{
    protected static array $requestPossibleFields = [
        'field_1' => null,
        'field_2' => null,
    ];
}

// when unauthorized fields
try {
    CustomRequest::createFromPayload([
        'field_1' => 1,
        'field_2' => 'value',
        'field_3' => new stdClass(),
    ]);
} catch (BadRequestContentException $exception) {
    // Handle unauthorized fields
    dd($exception->getErrors()); // ["field_3"]
}

// when missing fields
try {
    CustomRequest::createFromPayload([
        'field_2' => 'value',
    ]);
} catch (BadRequestContentException $exception) {
    // Handle missing fields
    dd($exception->getErrors()); // ["field_1"]
}

// when everything is good
$request = CustomRequest::createFromPayload([
    'field_1' => 1,
    'field_2' => 'value',
]);

dd($request->getRequestId()); // 6d326314-f527-483c-80df-7c157acdb95b
dd($request->getRequestData()); // ['field_1' => 1, 'field_2' => 'value']

// or with nested request parameters
final class CustomRequest extends Request implements CustomRequestInterface
{
    protected static array $requestPossibleFields = [
        'field_1' => null,
        'field_2' => null,
        'field_3' => null,
        'field_4' => [
            'field_5' => [
                'field_6' => '',
            ],
        ],
    ];
}

try {
    CustomRequest::createFromPayload([
        'field_1' => 1,
        'field_2' => 'value',
        'field_3' => new stdClass(),
        'field_4' => [
            'field_5' => [],
        ],
    ]);
} catch (BadRequestContentException $exception) {
    // Handle missing fields
    dd($exception->getErrors()); // ['field_4.field_5.field_6']
}
```

2. Creating a custom usecase

- Extends `\Cleancoders\Core\Usecase\Usecase` and implements `\Cleancoders\Core\Usecase\UsecaseInterface` to create
  your use cases.
- Implement the `execute` method for the use case logic.

```php
<?php

declare(strict_types=1);

use Cleancoders\Core\Response\Response;
use Cleancoders\Core\Response\StatusCode;
use Cleancoders\Core\Usecase\Usecase;
use Cleancoders\Core\Usecase\UsecaseInterface;

interface CustomUsecaseInterface extends UsecaseInterface
{

}

// with request and presenter with empty response
final class CustomUsecase extends Usecase implements CustomUsecaseInterface
{
    public function execute(): void
    {
        // get request data
        $requestData = $this->getRequestData();
        
        // process your business logic here
        
        // send usecase response without content
        $this->presenter->present(Response::create());
    }
}

// or with response

final class CustomUsecase extends Usecase implements CustomUsecaseInterface
{
    public function execute(): void
    {
        $requestData = $this->getRequestData(); // return your request data
        
        // process your business logic here
        
        // send usecase response with content
        $this->presenter->present(Response::create(
            success: true,
            statusCode: StatusCode::OK,
            message: 'success.response',
            data: [
                'field' => 'value',
            ]
        ));
    }
}

// without request
final class CustomUsecase extends Usecase implements CustomUsecaseInterface
{
    public function execute(): void
    {
        // get request data
        $requestData = $this->getRequestData(); // return []
        
        // process your business logic here
        
        // send usecase response with content
        $this->presenter->present(Response::create(
            success: true,
            statusCode: StatusCode::OK,
            message: 'success.response',
            data: [
                'field' => 'value',
            ]
        ));
    }
}

// with request and without presenter
final class CustomUsecase extends Usecase implements CustomUsecaseInterface
{
    public function execute(): void
    {
        // get request data
        $requestData = $this->getRequestData();
        
        // process your business logic here
        
        // $this->presenter will be null here
    }
}
```

3. Create the custom presenter

- Extends `\Cleancoders\Core\Presenter\Presenter` to create `presenters`.

```php
<?php

declare(strict_types=1);

use Cleancoders\Core\Presenter\Presenter;
use Cleancoders\Core\Presenter\PresenterInterface;

interface CustomPresenterInterface extends PresenterInterface
{

}

final class CustomPresenter extends Presenter implements CustomPresenterInterface
{
  // you can override parent methods here to customize them
}
```

4. Executing the Usecase

Instantiate your custom usecase, set your custom request and custom presenter, and execute the usecase.

```php
<?php

declare(strict_types=1);

use Cleancoders\Core\Exception\Exception;

try {
    $customRequest = CustomRequest::createFromPayload([
        'field_1' => 1,
        'field_2' => 'value',
    ]);
    $customPresenter = new CustomPresenter();
    $customUsecase = new CustomUsecase();
    $customUsecase
        ->setRequest($customRequest)
        ->setPresenter($customPresenter)
        ->execute();
    
    // now you can get usecase response from presenter
    $response = $customPresenter->getResponse();
    
    dd($response->isSuccess()); // true
    dd($response->getStatusCode()); // 200
    dd($response->getMessage()); // 'success.response'
    dd($response->getData()); // ['field' => 'value']
    dd($response->get('field')); // 'value'
    dd($response->get('unknown_field')); // null
} catch (Exception $exception) {
    dd($exception);
}

// without request
try {
    $customPresenter = new CustomPresenter();
    $customUsecase = new CustomUsecase();
    $customUsecase
        ->setPresenter($customPresenter)
        ->execute();
    
    // now you can get usecase response from presenter
    $response = $customPresenter->getResponse();
    
    dd($response->isSuccess()); // true
    dd($response->getStatusCode()); // 200
    dd($response->getMessage()); // 'success.response'
    dd($response->getData()); // ['field' => 'value']
    dd($response->get('field')); // 'value'
    dd($response->get('unknown_field')); // null
} catch (Exception $exception) {
    dd($exception);
}

// without presenter and without request
try {
    $customUsecase = new CustomUsecase();
    $customUsecase->execute(); // return anything (void)
} catch (Exception $exception) {
    dd($exception);
}
```

## Example with symfony controller

```php
<?php

declare(strict_types=1);

namespace Cleancoders\Controller;

use Cleancoders\Core\Exception\Exception;
use Cleancoders\Core\Response\StatusCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: "_app", methods: 'GET')]
final class CustomController extends AbstractController
{
    public function __construct(
        private readonly CustomPresenterInterface $customPresenter,
        private readonly CustomUsecaseInterface $customUsecase,
        private readonly CustomRequestInterface $customRequest
    ) {}

    public function __invoke(SymfonyRequest $request): JsonResponse
    {
        try {
            $this
                ->customUsecase
                ->setRequest($this->customRequest::createFromPayload([
                    'field_1' => $request->get('field_1'),
                    'field_2' => $request->get('field_2'),
                ])
                ->setPresenter($this->customPresenter)
                ->execute();
            
            $response = $this->customPresenter->getResponse();
        } catch (Exception $exception) {
            // you also can get errors for log: $exception->getErrorsForLog()
            
            return $this->json($exception->getErrors(), $exception->getCode());
        }

        return $this->json($response->getData(), $response->getStatusCode());
    }
}

// or

#[Route('/', name: "_app", methods: 'POST')]
final class CustomController extends AbstractController
{
    public function __construct(
        private readonly CustomUsecaseInterface $customUsecase,
        private readonly CustomRequestInterface $customRequest
    ) {}

    public function __invoke(SymfonyRequest $request): JsonResponse
    {
        try {
            $this
                ->customUsecase
                ->setRequest($this->customRequest::createFromPayload([
                    'field_1' => $request->get('field_1'),
                    'field_2' => $request->get('field_2'),
                ])
                ->execute();
        } catch (Exception $exception) {
            // you also can get errors for log: $exception->getErrorsForLog()
            
            return $this->json($exception->getErrors(), $exception->getCode());
        }

        return $this->json([], StatusCode::OK->getValue());
    }
}
```

## Example with laravel controller

```php
<?php

declare(strict_types=1);

namespace Cleancoders\Controller;

use Cleancoders\Core\Exception\Exception;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;

final class CustomController extends Controller
{
    public function __construct(
        private readonly CustomUsecaseInterface $customUsecase
    ) {}

    public function __invoke(LaravelRequest $request): LaravelResponse
    {
        try {
            $this->customUsecase->execute();
        } catch (Exception $exception) {
            // you also can get errors for log: $exception->getErrorsForLog()
            
            return response()->json($exception->getErrors(), $exception->getCode());
        }

        return response()->json([], StatusCode::OK->getValue());
    }
}

// or

final class CustomController extends Controller
{
    public function __construct(
        private readonly CustomPresenterInterface $customPresenter,
        private readonly CustomUsecaseInterface $customUsecase
    ) {}

    public function __invoke(LaravelRequest $request): LaravelResponse
    {
        try {
            $this
                ->customUsecase
                ->setPresenter($this->customPresenter)
                ->execute();
            
            $response = $this->customPresenter->getResponse();
        } catch (Exception $exception) {
            // you also can get errors for log: $exception->getErrorsForLog()
            
            return response()->json($exception->getErrors(), $exception->getCode());
        }

        return response()->json($response->getData(), $response->getStatusCode());
    }
}
```

## Units Tests

You also can execute unit tests.

```
$ make tests
```

## License

- Written and copyrighted ©2023-present by Ulrich Geraud AHOGLA. <iamcleancoder@gmail.com>
- Clean architecture core is open-sourced software licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php)