<?php

declare(strict_types=1);

namespace Framework\Http;

use Framework\Exception\NotFoundException;
use Framework\Http\Contract\RequestInterface;
use Framework\Http\Exception\InvalidHttpException;

/**
 * Factory permettant de créer dynamiquement une instance d'une classe implémentant RequestInterface.
 */
class RequestFactory
{
    /**
     * Instancie dynamiquement une classe de requête et s'assure qu'elle implémente RequestInterface.
     *
     * @param string $className le nom complet de la classe à instancier
     *
     * @throws NotFoundException si la classe n'existe pas
     * @throws InvalidHttpException si la classe n'implémente pas RequestInterface
     *
     * @return RequestInterface L'instance de la classe demandée
     */
    public function __invoke(string $className): RequestInterface
    {
        if (!\class_exists($className)) {
            throw new NotFoundException(\sprintf("La classe %s n'existe pas.", $className));
        }

        $request = new $className();

        if (!$request instanceof RequestInterface) {
            throw InvalidHttpException::forMissingInterface($className, RequestInterface::class);
        }

        return $request;
    }
}
