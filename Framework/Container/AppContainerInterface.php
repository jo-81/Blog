<?php

declare(strict_types=1);

namespace Framework\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Interface AppContainerInterface.
 *
 * Définit un conteneur d'injection de dépendances avancé,
 * compatible PSR-11, permettant la gestion des singletons,
 * des factories, des liaisons abstraites/concrètes, l'instanciation
 * automatique et la configuration groupée des services.
 */
interface AppContainerInterface extends PsrContainerInterface
{
    /*
     * Enregistre un service ou une instance unique (singleton) dans le conteneur.
     * À chaque appel, la même instance sera retournée.
     *
     * @param string $id identifiant unique du service
     * @param mixed $concrete instance concrète ou closure/factory qui retourne l’instance
     */
    public function singleton(string $id, mixed $concrete): void;

    /*
     * Lie une abstraction (interface ou classe abstraite) à une implémentation concrète.
     * Permet au conteneur de savoir quelle classe instancier pour une dépendance donnée.
     *
     * @param string $abstract nom de l’interface ou de la classe abstraite
     * @param string $concrete nom de la classe concrète à instancier
     */
    public function bind(string $abstract, string $concrete): void;

    /*
     * Instancie une classe en résolvant automatiquement ses dépendances.
     * Permet de passer des paramètres spécifiques au constructeur.
     *
     * @param string $className nom de la classe à instancier
     * @param array<string, mixed> $parameters paramètres à injecter au constructeur (optionnel)
     *
     * @return object instance de la classe demandée
     */
    public function make(string $className, array $parameters = []): object;

    /**
     * Ajoute ou fusionne des définitions de services au conteneur.
     *
     * Les définitions peuvent être des instances, des factories, des autowires, etc.
     * Si une clé existe déjà, elle sera écrasée par la nouvelle définition.
     *
     * @param array<string, mixed> $definitions
     *                                          Tableau associatif de définitions de services à ajouter au conteneur
     */
    public function addDefinitions(array $definitions): void;

    /*
     * Configure le conteneur avec un ensemble de définitions de services.
     * Permet d’enregistrer plusieurs services, singletons, liaisons en une seule opération.
     *
     * Exemple de format accepté :
     * [
     *     'service1' => ['type' => 'singleton', 'class' => MyClass::class],
     *     'service2' => ['type' => 'bind', 'concrete' => OtherClass::class],
     *     'service3' => ['type' => 'value', 'value' => 42],
     *     'service4' => ['type' => 'class', 'class' => AnotherClass::class],
     * ]
     *
     * @param array<mixed> $definitions tableau associatif de définitions de services
     */
    public function configure(array $definitions): void;

    /**
     * Configure le conteneur depuis un dossier.
     *
     * @param string $directory Le dossier de configuration
     */
    public function configureWithDirectory(string $directory): void;
    
    /**
     * Vide les définitions
     *
     * @return void
     */
    public function clear(): void;
}
