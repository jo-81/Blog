<?php

declare(strict_types=1);

namespace App\Tests\Framework\Container\Fixtures;

class WithDep
{
    public function __construct(public Dep $dep) {}
}
