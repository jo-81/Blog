<?php

declare(strict_types=1);

namespace App\Tests\Framework\Http;

use PHPUnit\Framework\TestCase;
use Framework\Http\ResponseFactory;
use Framework\Exception\NotFoundException;
use Framework\Http\Contract\ResponseInterface;
use Framework\Http\Exception\InvalidHttpException;

/**
 * @internal
 */
class ResponseFactoryTest extends TestCase
{
    public function testExceptionWhenClassNotExist(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("La classe NotExist n'existe pas.");

        $request = new ResponseFactory();
        $request('NotExist');
    }

    public function testExceptionWhenClassNotImplementResponseInterface(): void
    {
        $this->expectException(InvalidHttpException::class);
        $this->expectExceptionMessage(sprintf(
            "La classe %s n'implémente pas l'interface %s.",
            \stdClass::class,
            ResponseInterface::class,
        ));

        $request = new ResponseFactory();
        $request(\stdClass::class);
    }

    public function testReturnResponseInterface(): void
    {
        $requestMock = $this->createMock(ResponseInterface::class);
        $request = new ResponseFactory();

        $this->assertInstanceOf(ResponseInterface::class, $request(get_class($requestMock)));
    }
}
