<?php

declare(strict_types=1);

namespace App\Tests\Framework\Container\Fixtures;

class WithScalar
{
    public function __construct(public int $value) {}
}
