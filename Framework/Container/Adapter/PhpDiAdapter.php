<?php

declare(strict_types=1);

namespace Framework\Container\Adapter;

use DI\Container;
use DI\ContainerBuilder;
use DI\NotFoundException as DINotFoundException;
use Framework\Container\AppContainerInterface;
use Framework\Container\Exception\ContainerArgumentException;
use Framework\Container\Exception\NotFoundException;

class PhpDiAdapter implements AppContainerInterface
{
    private Container $container;

    /** @var array<string, mixed> */
    private array $definitions = [];

    private bool $needsRebuild = false;

    public function __construct(private bool $isProduction = false)
    {
        $this->rebuildContainer();
    }

    public function get(string $id): mixed
    {
        $this->rebuildIfNeeded();

        try {
            return $this->container->get($id);
        } catch (DINotFoundException $th) {
            throw new NotFoundException($th->getMessage(), $th->getCode(), $th);
        } catch (\Exception $e) {
            throw new ContainerArgumentException("Erreur lors de la récupération du service '{$id}': " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function has(string $id): bool
    {
        $this->rebuildIfNeeded();

        try {
            return $this->container->has($id);
        } catch (\Exception $e) {
            throw new ContainerArgumentException("Erreur lors de la vérification du service '{$id}': " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function clear(): void
    {
        $this->definitions = [];
        $this->needsRebuild = true;
        $this->rebuildIfNeeded();
    }

    public function singleton(string $id, mixed $concrete): void
    {
        if (is_string($concrete)) {
            $this->definitions[$id] = \DI\autowire($concrete);
        } elseif ($concrete instanceof \Closure || is_callable($concrete)) {
            $this->definitions[$id] = \DI\factory($concrete);
        } else {
            $this->definitions[$id] = \DI\value($concrete);
        }
        $this->needsRebuild = true;
    }

    public function bind(string $abstract, string $concrete): void
    {
        $this->definitions[$abstract] = \DI\autowire($concrete);
        $this->needsRebuild = true;
    }

    /**
     * make.
     *
     * @param array<string, mixed> $parameters
     */
    public function make(string $className, array $parameters = []): object
    {
        $this->rebuildIfNeeded();

        try {
            return $this->container->make($className, $parameters);
        } catch (DINotFoundException $th) {
            throw new NotFoundException($th->getMessage(), $th->getCode(), $th);
        } catch (\Exception $e) {
            throw new ContainerArgumentException("Erreur lors de l'instanciation de '{$className}': " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function addDefinitions(array $definitions): void
    {
        $this->definitions = array_merge($this->definitions, $definitions);
        $this->needsRebuild = true;
    }

    /**
     * configure.
     *
     * @param array<mixed> $definitions
     */
    public function configure(array $definitions): void
    {
        foreach ($definitions as $id => $definition) {
            $this->processDefinition($id, $definition);
        }
        $this->needsRebuild = true;
    }

    public function configureWithDirectory(string $directory): void
    {
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new ContainerArgumentException("Le fichier de définitions n'existe pas ou n'est pas lisible : {$directory}");
        }

        $files = $this->listFilesInDirectory($directory, '.php');
        $definitions = [];

        foreach ($files as $file) {
            $definitionsFile = require $file;

            if (!is_array($definitionsFile)) {
                throw new ContainerArgumentException('Le fichier de définitions doit retourner un tableau.');
            }

            $definitions = array_merge($definitions, $definitionsFile);
        }

        $this->configure($definitions);
    }

    /**
     * Récupère la liste des fichiers PHP dans un dossier donné.
     *
     * @param string $directory Chemin du dossier à parcourir
     * @param string $extension Extension des fichiers à filtrer (ex: '.php'), optionnel
     *
     * @throws ContainerArgumentException si le dossier n'existe pas ou n'est pas lisible
     *
     * @return string[] Liste des chemins de fichiers trouvés
     */
    private function listFilesInDirectory(string $directory, string $extension = ''): array
    {
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new ContainerArgumentException("Le dossier n'existe pas ou n'est pas lisible : {$directory}");
        }

        $files = [];

        foreach (array_diff(scandir($directory), ['.', '..']) as $file) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_file($filePath) && ($extension === '' || substr($file, -strlen($extension)) === $extension)) {
                $files[] = $filePath;
            }
        }

        return $files;
    }

    /**
     * processDefinition.
     *
     * @param array<mixed> $definition
     */
    private function processDefinition(string $id, array $definition): void
    {
        $type = $definition['type'] ?? 'class';
        $lazy = $definition['lazy'] ?? false;

        switch ($type) {
            case 'singleton':
                $service = \DI\autowire($definition['class']);

                if ($lazy) {
                    $service->lazy();
                }
                $this->definitions[$id] = $service;
                break;
            case 'bind':
                $service = \DI\autowire($definition['concrete']);

                if ($lazy) {
                    $service->lazy();
                }
                $this->definitions[$id] = $service;
                break;
            case 'class':
                $service = \DI\autowire($definition['class']);

                if ($lazy) {
                    $service->lazy();
                }
                $this->definitions[$id] = $service;
                break;
            case 'value':
                $this->definitions[$id] = $definition['value'];
                break;
            default:
                throw new ContainerArgumentException("Type de définition non supporté: {$type}");
        }
    }

    private function rebuildContainer(): void
    {
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true)
            ->useAttributes(true)
            ->addDefinitions($this->definitions)
        ;

        if ($this->isProduction) {
            $builder->enableCompilation(__DIR__ . '/var/cache')
                ->writeProxiesToFile(true, __DIR__ . '/var/proxies')
            ;
        }

        $this->container = $builder->build();
        $this->needsRebuild = false;
    }

    private function rebuildIfNeeded(): void
    {
        if ($this->needsRebuild) {
            $this->rebuildContainer();
        }
    }
}
