<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Armand
 * Date: 12/07/13
 * Time: 16:59
 * To change this template use File | Settings | File Templates.
 */

namespace Ioc;


class Container
{
    /**
     * @var SimpleXMLElement
     */
    private $_configurationIoc;


    /**
     * @var array|null
     */
    private $_componentList = null;

    /**
     * @param tring $iocFilePath
     */
    public function __construct($iocFilePath)
    {
        if (!file_exists($iocFilePath) || !is_readable($iocFilePath)) {
            throw new \Exception('Impossible d\'accéder au fichier de définition de l\IOC.');
        }

        $this->_configurationIoc = simplexml_load_file($iocFilePath);

        $this->_init();
    }

    /**
     *
     */
    private function _init()
    {
        // Génération de la liste des composants.
        $componentList = array();
        foreach ($this->_configurationIoc->children() as $componentConfiguration) {
            /* @var $componentConfiguration SimpleXMLElement */

            $componentName = (string)$componentConfiguration->attributes()->name;
            $componentClass = (string)$componentConfiguration->attributes()->class;
            $componentNamespace = (string)$componentConfiguration->attributes()->namespace;
            $componentDependencies = (array)$componentConfiguration->children()->dependecies->component;

            $componentInstance = new Component($componentName, $componentClass, $componentNamespace, $componentDependencies);

            $this->_componentList[$componentInstance->name] = $componentInstance;
        }

        // Vérification des dependances.
        foreach ($this->_componentList as $component) {
            /* @var $component Component */
            $this->_checkComponentDependenciesExistance($component);
        }

        foreach ($this->_componentList as $component) {
            /* @var $component Component */
            $this->_checkComponentCyclicDependentiesRecurcive($component);
        }
    }

    /**
     * @param Component $component
     * @throws \Exception
     */
    private function _checkComponentDependenciesExistance(Component $component)
    {
        foreach ($component->dependencies as $dependency) {
            if (!isset($this->_componentList[$dependency])) {
                throw new \Exception(sprintf('Le composant "%s" dépend d\'un composant inconnu : "%s".', $component->name, $dependency));
            }
        }
    }

    /**
     * @param Component $component
     * @param array $componentNameHistory
     * @throws \Exception
     */
    private function _checkComponentCyclicDependentiesRecurcive(Component $component, array $componentNameHistory = array())
    {
        foreach ($component->dependencies as $dependency) {
            // Détection de la dépendance cyclique.
            if (in_array($dependency, $componentNameHistory)) {
                array_unshift($componentNameHistory, $component->name);
                throw new \Exception(sprintf('Dépendance cyclique détecté : "%s".', implode(' >> ', $componentNameHistory)));
            }

            // Aucune dépendance cyclique, on vérifie le composant courant.
            $currentComponentDependency = $this->_componentList[$dependency];
            $componentNameHistory[] = $currentComponentDependency->name;

            $this->_checkComponentCyclicDependentiesRecurcive($currentComponentDependency, $componentNameHistory);
        }
    }

    /**
     * @param string $basePath
     * @param string $fileExtension
     * @return array
     */
    public function generateComponentsFilePathList($basePath, $fileExtension)
    {
        $componentsFilePathList = array();

//        $componentsFilePathList[] =

        foreach ($this->_componentList as $component) {
            /* @var $component Component */

            $componentsFilePathList[] = $component->getFilePath($basePath, $fileExtension);
        }

        return $componentsFilePathList;
    }

    public function getContainerDefinitionInJson() {
        var_dump(json_encode($this->_componentList, JSON_PRETTY_PRINT));
    }

}