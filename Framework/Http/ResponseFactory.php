<?php

declare(strict_types=1);

namespace Framework\Http;

use Framework\Exception\NotFoundException;
use Framework\Http\Contract\ResponseInterface;
use Framework\Http\Exception\InvalidHttpException;

/**
 * Factory permettant de créer dynamiquement une instance d'une classe implémentant ResponseInterface.
 */
class ResponseFactory
{
    /**
     * Instancie dynamiquement une classe de réponse et s'assure qu'elle implémente ResponseInterface.
     *
     * @param string $className le nom complet de la classe à instancier
     *
     * @throws NotFoundException si la classe n'existe pas
     * @throws InvalidHttpException si la classe n'implémente pas ResponseInterface
     *
     * @return ResponseInterface L'instance de la classe demandée
     */
    public function __invoke(string $className): ResponseInterface
    {
        if (!\class_exists($className)) {
            throw new NotFoundException(\sprintf("La classe %s n'existe pas.", $className));
        }

        $request = new $className();

        if (!$request instanceof ResponseInterface) {
            throw InvalidHttpException::forMissingInterface($className, ResponseInterface::class);
        }

        return $request;
    }
}
