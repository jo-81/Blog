<?php

declare(strict_types=1);

namespace App\Test\Framework\Container\Adapter;

use App\Tests\Framework\Container\Fixtures\Dep;
use App\Tests\Framework\Container\Fixtures\FileLogger;
use App\Tests\Framework\Container\Fixtures\LoggerInterface;
use App\Tests\Framework\Container\Fixtures\WithDep;
use App\Tests\Framework\Container\Fixtures\WithScalar;
use Framework\Container\Adapter\PhpDiAdapter;
use Framework\Container\AppContainerInterface;
use Framework\Container\ContainerFactory;
use Framework\Container\Exception\ContainerArgumentException;
use Framework\Container\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PhpDiAdapterTest extends TestCase
{
    private AppContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = ContainerFactory::create(PhpDiAdapter::class);
    }

    /**
     * testHasKey.
     */
    public function testHasKey(): void
    {
        $this->assertFalse($this->container->has('not-exist'));
    }

    /**
     * testWhenKeyNotExist.
     */
    public function testWhenKeyNotExist(): void
    {
        $this->expectException(NotFoundException::class);

        $this->container->get('not-found');
    }

    /**
     * testClear.
     */
    public function testClear(): void
    {
        $this->container->addDefinitions(['foo' => 'bar']);

        $this->assertTrue($this->container->has('foo'));

        $this->container->clear();

        $this->assertFalse($this->container->has('foo'));
    }

    /**
     * testSingletonWithClassStringReturnsSameInstance.
     */
    public function testSingletonWithClassStringReturnsSameInstance(): void
    {
        $this->container->singleton('service', \stdClass::class);

        $a = $this->container->get('service');
        $b = $this->container->get('service');

        $this->assertSame($a, $b);
        $this->assertInstanceOf(\stdClass::class, $a);
    }

    /**
     * testSingletonWithInstanceReturnsSameInstance.
     */
    public function testSingletonWithInstanceReturnsSameInstance(): void
    {
        // Test avec une instance existante
        $instance = new \stdClass();
        $this->container->singleton('service', $instance);

        $result = $this->container->get('service');

        $this->assertSame($instance, $result);
    }

    /**
     * testSingletonWithClosureReturnsSameInstance.
     */
    public function testSingletonWithClosureReturnsSameInstance(): void
    {
        $this->container->singleton('service', function () {
            return new \stdClass();
        });

        $a = $this->container->get('service');
        $b = $this->container->get('service');

        $this->assertSame($a, $b);
        $this->assertInstanceOf(\stdClass::class, $a);
    }

    /**
     * testBind.
     */
    public function testBind(): void
    {
        $this->container->bind(LoggerInterface::class, FileLogger::class);

        $instance = $this->container->get(LoggerInterface::class);
        $this->assertInstanceOf(LoggerInterface::class, $instance);
        $this->assertInstanceOf(FileLogger::class, $instance);
    }

    /**
     * testMakeInstantiatesFileLogger.
     */
    public function testMakeInstantiatesFileLogger(): void
    {
        $instance = $this->container->make(FileLogger::class);

        $this->assertInstanceOf(FileLogger::class, $instance);
    }

    /**
     * testMakeResolvesDependencies.
     */
    public function testMakeResolvesDependencies(): void
    {
        $this->container->singleton(WithDep::class, new WithDep(new Dep()));

        $instance = $this->container->make(WithDep::class);

        $this->assertInstanceOf(WithDep::class, $instance);
        $this->assertInstanceOf(Dep::class, $instance->dep);
    }

    /**
     * testMakeWithScalarParameter.
     */
    public function testMakeWithScalarParameter(): void
    {
        $instance = $this->container->make(WithScalar::class, ['value' => 42]);
        $this->assertInstanceOf(WithScalar::class, $instance);
        $this->assertEquals(42, $instance->value);
    }

    /**
     * testAddDefinitionsRegistersServices.
     */
    public function testAddDefinitionsRegistersServices(): void
    {
        $definitions = [
            'foo' => \DI\value('bar'),
        ];

        $this->container->addDefinitions($definitions);

        $this->assertTrue($this->container->has('foo'));
        $this->assertSame('bar', $this->container->get('foo'));
    }

    /**
     * testConfigure.
     */
    public function testConfigure(): void
    {
        $definitions = [
            'id' => ['type' => 'value', 'value' => 1],
            'singleton' => ['type' => 'singleton', 'class' => \stdClass::class],
            'class' => ['type' => 'class', 'class' => \stdClass::class],
            'bind' => ['type' => 'bind', 'concrete' => FileLogger::class],
            'not-type' => ['class' => \stdClass::class],
        ];

        $this->container->configure($definitions);

        $this->assertSame(1, $this->container->get('id'));
        $this->assertInstanceOf(\stdClass::class, $this->container->get('singleton'));
        $this->assertInstanceOf(\stdClass::class, $this->container->get('class'));
        $this->assertInstanceOf(\stdClass::class, $this->container->get('not-type'));
        $this->assertInstanceOf(FileLogger::class, $this->container->get('bind'));
    }

    /**
     * testConfigureWithBadIdKey.
     */
    public function testConfigureWithBadIdKey(): void
    {
        $this->expectException(ContainerArgumentException::class);

        $this->container->configure(['id' => ['type' => 'invalide', 'value' => 1]]);
    }

    public function testConfigureWithDirectory(): void
    {
        $directory = dirname(__DIR__) . '/Fixtures/config';

        $this->container->configureWithDirectory($directory);

        $this->assertSame(1, $this->container->get('id'));
    }

    public function testConfigureWithDirectoryNotExist(): void
    {
        $this->expectException(ContainerArgumentException::class);

        $directory = dirname(__DIR__) . '/Fixtures/conf';
        $this->container->configureWithDirectory($directory);
    }
}
