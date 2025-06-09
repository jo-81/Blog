<?php

declare(strict_types=1);

namespace Framework\Http\Exception;

class InvalidRequestException extends \RuntimeException
{
    public static function forMissingInterface(string $className, string $interface): self
    {
        return new self(sprintf(
            "La classe %s n'implémente pas l'interface %s.",
            $className,
            $interface,
        ));
    }
}
