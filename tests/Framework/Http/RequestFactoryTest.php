<?php

declare(strict_types=1);

namespace App\Tests\Framework\Http;

use PHPUnit\Framework\TestCase;
use Framework\Http\RequestFactory;
use Framework\Exception\NotFoundException;
use Framework\Http\Contract\RequestInterface;
use Framework\Http\Exception\InvalidHttpException;

/**
 * @internal
 */
class RequestFactoryTest extends TestCase
{
    public function testExceptionWhenClassNotExist(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("La classe NotExist n'existe pas.");

        $request = new RequestFactory();
        $request('NotExist');
    }

    public function testExceptionWhenClassNotImplementRequestInterface(): void
    {
        $this->expectException(InvalidHttpException::class);
        $this->expectExceptionMessage(sprintf(
            "La classe %s n'implémente pas l'interface %s.",
            \stdClass::class,
            RequestInterface::class,
        ));

        $request = new RequestFactory();
        $request(\stdClass::class);
    }

    public function testReturnRequestInterface(): void
    {
        $requestMock = $this->createMock(RequestInterface::class);
        $request = new RequestFactory();

        $this->assertInstanceOf(RequestInterface::class, $request(get_class($requestMock)));
    }
}
