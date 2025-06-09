<?php

declare(strict_types=1);

namespace App\Tests\Framework\Container;

use PHPUnit\Framework\TestCase;
use Framework\Container\ContainerFactory;
use Framework\Container\AppContainerInterface;
use Framework\Container\Exception\ContainerException;

/**
 * @internal
 */
class ContainerFactoryTest extends TestCase
{
    /**
     * testExceptionWhenClassNotExist.
     */
    public function testExceptionWhenClassNotExist(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("La classe NotExist n'existe pas");

        ContainerFactory::create('NotExist');
    }

    /**
     * testExceptionWhenClassNotImplementAppContainerInterface.
     */
    public function testExceptionWhenClassNotImplementAppContainerInterface(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage(\sprintf("La classe %s n'implémente pas la AppContainerInterface", \stdClass::class));

        ContainerFactory::create(\stdClass::class);
    }

    /**
     * testReturnAppContainerInterface.
     */
    public function testReturnAppContainerInterface(): void
    {
        $containerMock = $this->createMock(AppContainerInterface::class);

        $container = ContainerFactory::create(\get_class($containerMock));

        $this->assertInstanceOf(AppContainerInterface::class, $container);
    }
}
