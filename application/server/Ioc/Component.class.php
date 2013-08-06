<?php

namespace Ioc;

class Component
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var array
     */
    public $dependencies;

    public function __construct($name, $class, $namespace, array $dependendies)
    {
        $name = trim($name);
        if (empty($name)) {
            throw new \Exception('Un composant n\'a pas de nom.');
        }

        $class = trim($class);
        if (empty($class)) {
            throw new \Exception(sprintf('Le composant %s ne possède pas de class associé.', $name));
        }

        $namespace = trim($namespace);
        if (empty($namespace)) {
            throw new \Exception(sprintf('Le composant %s ne possède pas de namespace.', $name));
        }

        foreach ($dependendies as $key => $dependency) {
            $dependendies[$key] = trim($dependency);

            if (empty($dependency)) {
                throw new \Exception(sprintf('Le composant %s possède une dépendance vide.', $name));
            }
        }

        $this->name = $name;
        $this->class = $class;
        $this->namespace = $namespace;
        $this->dependencies = $dependendies;
    }

    /**
     * @param $basePath string
     * @param $fileExtension string
     * @return string
     */
    public function getFilePath($basePath, $fileExtension)
    {
        $directoryPath = str_replace('.', DIRECTORY_SEPARATOR, $this->namespace);
        $filename = $this->class;

        return $basePath . DIRECTORY_SEPARATOR . $directoryPath . DIRECTORY_SEPARATOR . $filename . $fileExtension;
    }

}