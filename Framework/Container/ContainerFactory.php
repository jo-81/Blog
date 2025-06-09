<?php

declare(strict_types=1);

namespace Framework\Container;

use Framework\Container\Exception\ContainerException;

class ContainerFactory
{
    /**
     * Crée une instance de conteneur à partir d'un nom de classe.
     *
     * @param string $containerName Nom de la classe du conteneur à instancier
     *
     * @throws ContainerException Si la classe n'existe pas ou n'implémente pas l'interface
     */
    public static function create(string $containerName): AppContainerInterface
    {
        if (!\class_exists($containerName)) {
            throw new ContainerException(\sprintf("La classe %s n'existe pas", $containerName));
        }

        $container = new $containerName();

        if (!is_a($container, AppContainerInterface::class)) {
            throw new ContainerException(\sprintf("La classe %s n'implémente pas la AppContainerInterface", $containerName));
        }

        return $container;
    }
}
