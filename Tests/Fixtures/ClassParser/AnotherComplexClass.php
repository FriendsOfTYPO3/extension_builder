<?php
/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Component Manager of the extension 'gimmefive'. This is a backport of the Component Manager of FLOW3. It's based
 * on code mainly written by Robert Lemke. Thanx to the FLOW3 team for all the great stuff!
 */
class Tx_ExtensionBuilder_Tests_Examples_ClassParser_AnotherComplexClass
{
    const PACKAGE_PREFIX = 'Tx';
    const THIS_PACKAGE_KEY = 'GimmeFive';
    const DIRECTORY_CLASSES = 'Classes/';
    const DIRECTORY_CONFIGURATION = 'Configuration/';
    const DIRECTORY_LANGUAGES = 'Resources/Language/';
    const DIRECTORY_TEMPLATES = 'Resources/Template/';
    const SCOPE_PROTOTYPE = 'prototype';
    const SCOPE_SINGLETON = 'singleton';

    /**
     * @var string[]
     */
    protected $debugMessages = [];

    /**
     * an array of fields that if changed require a reindexing of all the events
     *
     * @var array
     */
    protected static $fieldsRequiringReindexing = [
        'recurrance_type',
        'recurrance_subtype',
        'recurrance_until',
        'start_day',
        'start_time',
        'end_day',
        'end_time',
        'pid',
        'hidden',
        'deleted'
    ];

    /**
     * @var \Tx_ExtensionBuilder_Tests_Examples_ClassParser_AnotherComplexClass
     */
    private static $instance = null;

    protected $classFiles;
    protected $packagePath;

    protected $componentObjects = []; // the object cache
    protected $componentConfigurations = []; // the configuration cache
    protected $tsConf; // TypoScript config

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Tx_ExtensionBuilder_Tests_Examples_ClassParser_AnotherComplexClass();
        }
        return self::$instance;
    }

    protected function __construct()
    {
        $this->loadTypoScriptConfig();
        spl_autoload_register([$this, 'loadClass']);
    }

    private function __clone()
    {
    }

    /**
     * Loads the TypoScript config/setup for the formhandler on the current page.
     */
    private function loadTypoScriptConfig()
    {
        $sysPageObj = GeneralUtility::makeInstance('t3lib_pageSelect');
        if (!$GLOBALS['TSFE']->sys_page) {
            $GLOBALS['TSFE']->sys_page = $sysPageObj;
        }
        $rootLine = $sysPageObj->getRootLine($GLOBALS['TSFE']->id);
        $TSObj = GeneralUtility::makeInstance('t3lib_tsparser_ext');
        $TSObj->tt_track = 0;
        $TSObj->init();
        $TSObj->runThroughTemplates($rootLine);
        $TSObj->generateConfig();
        $conf = $TSObj->setup['plugin.']['Tx_Formhandler.']['settings.'];
        $this->tsConf = $conf;
    }

    /**
     * Returns a component object from the cache. If there is no object stored already, a new one is created and stored in the cache.
     *
     * @param string $componentName
     *
     * @author Robert Lemke <robert@typo3.org>
     * @author adapted for TYPO3v4 by Jochen Rau <jochen.rau@typoplanet.de>
     * @return object|\Tx_ExtensionBuilder_Tests_Examples_ClassParser_AnotherComplexClass
     * @throws \Exception
     */
    public function getComponent($componentName)
    {

        //Avoid component manager creating multiple instances of itself:
        if (get_class($this) == $componentName) {
            return $this;
        }

        if (!is_array($this->classFiles)) {
            $this->classFiles = [];
        }
        /*if ($this->componentObjectExists($componentName)) {
            $componentObject = $this->componentObjects[$componentName];
        } else */
        if (!array_key_exists($componentName, $this->classFiles)) {
            $this->loadClass($componentName);
            $componentObject = $this->createComponentObject($componentName, []);
            $this->putComponentObject($componentName, $componentObject);
        } else {
            $arguments =  array_slice(func_get_args(), 1, null, true); // array keys are preserved (TRUE) -> argument array starts with key=1
            $componentObject = $this->createComponentObject($componentName, $arguments);
        }
        return $componentObject;
    }

    /**
     * Requires a class file and instanciates a class.
     *
     * @param string $componentName
     * @param array $overridingConstructorArguments
     *
     * @return object
     * @throws \Exception
     * @throws \ReflectionException
     * @author Robert Lemke <robert@typo3.org>
     * @author adapted for TYPO3v4 by Jochen Rau <jochen.rau@typoplanet.de>
     */
    protected function createComponentObject($componentName, array $overridingConstructorArguments)
    {
        $componentObject = null;
        $componentConfiguration = $this->getComponentConfiguration($componentName);
        $className = $componentConfiguration['className'] ? $componentConfiguration['className'] : $componentName;

        if (!class_exists($className, true)) {
            throw new Exception('No valid implementation class for component "' . $componentName . '" found while building the component object (Class "' . $className . '" does not exist).');
        }

        $constructorArguments = $componentConfiguration['arguments.'] ? $componentConfiguration['arguments.'] : [];
        foreach ($overridingConstructorArguments as $index => $value) {
            $constructorArguments[$index] = $value;
        }
        $class = new ReflectionClass($className);
        $constructorArguments = $this->autoWireConstructorArguments($constructorArguments, $class);

        $injectedArguments = [];
        $preparedArguments = [];
        $this->injectConstructorArguments($constructorArguments, $injectedArguments, $preparedArguments);

        $instruction = '$componentObject = new ' . $className . '(';
        $instruction .= implode(', ', $preparedArguments);
        $instruction .= ');';

        eval($instruction);

        $scope = $this->getComponentScope($componentName, $componentConfiguration);

        switch ($scope) {
            case 'singleton' :
                $this->putComponentObject($componentName, $componentObject);
                break;
            case 'prototype' :
                break;
            default :
                throw new Exception('Support for scope "' . $scope . '" has not been implemented (yet)', 1167484148);
        }
        return $componentObject;
    }

    /**
     * If mandatory constructor arguments have not been defined yet, this function tries to autowire
     * them if possible.
     *
     * @param array $constructorArguments : Array of Tx_FLOW3_Component_ConfigurationArgument for the current component
     * @param \ReflectionClass $class : The component class which contains the methods supposed to be analyzed
     *
     * @return array The modified array of constructor arguments
     * @author Robert Lemke <robert@typo3.org>
     * @author adapted for TYPO3v4 by Jochen Rau <jochen.rau@typoplanet.de>
     * @throws \Exception
     */
    protected function autoWireConstructorArguments(array $constructorArguments, \ReflectionClass $class)
    {
        $className = $class->getName();
        $constructor = $class->getConstructor();
        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $parameterIndex => $parameter) {
                $index = $parameterIndex + 1;
                if (!isset($constructorArguments[$index])) {
                    try {
                        if ($parameter->isOptional()) {
                            $defaultValue = ($parameter->isDefaultValueAvailable()) ? $parameter->getDefaultValue() : null;
                            $constructorArguments[$index] = $defaultValue;
                        } elseif ($parameter->getClass() !== null) {
                            $constructorArguments[$index] = $parameter->getClass()->getName();
                        } elseif ($parameter->allowsNull()) {
                            $constructorArguments[$index] = null;
                        } else {
                            $this->debugMessages[] = 'Tried everything to autowire parameter $' . $parameter->getName() . ' in ' . $className . '::' . $constructor->getName() . '() but I saw no way.';
                        }
                    } catch (ReflectionException $exception) {
                        throw new Exception('While trying to autowire the parameter $' . $parameter->getName() . ' of the method ' . $className . '::' . $constructor->getName() . '() a ReflectionException was thrown. Please verify the definition of your constructor method in ' . $constructor->getFileName() . ' line ' . $constructor->getStartLine() . '. Original message: ' . $exception->getMessage());
                    }
                } else {
                    $this->debugMessages[] = 'Did not try to autowire parameter $' . $parameter->getName() . ' in ' . $className . '::' . $constructor->getName() . '() because it was already set.';
                }
            }
        } else {
            $this->debugMessages[] = 'Autowiring for class ' . $className . ' disabled because no constructor was found.';
        }
        return $constructorArguments;
    }

    /**
     * Checks and resolves dependencies of the constructor arguments and prepares an array of constructor
     * arguments (strings) which can be used in a "new" statement to instantiate the component.
     *
     * @param array $constructorArguments: Array of constructor arguments for the current component
     * @param array &$injectedArguments: An empty array passed by reference. Will contain instances of the components which were injected into the constructor
     * @param array &$preparedArguments: An empty array passed by reference: Will contain constructor parameters as strings to be used in a new statement
     * @return void
     * @author Robert Lemke <robert@typo3.org>
     * @author adapted for TYPO3v4 by Jochen Rau <jochen.rau@typoplanet.de>
     */
    public function injectConstructorArguments($constructorArguments, &$injectedArguments, &$preparedArguments)
    {
        foreach ($constructorArguments as $index => $constructorArgument) {
            // TODO Testing the prefix is not very sophisticated. Should be is_object()
            if (substr($constructorArgument, 0, 3) === self::PACKAGE_PREFIX . '_') {
                $value = $this->getComponent($constructorArgument);
            } else {
                $value = $constructorArgument;
            }
            if (is_string($value)) {
                $escapedValue = str_replace("'", "\\'", str_replace('\\', '\\\\', $value));
                $preparedArguments[] = "'" . $escapedValue . "'";
            } elseif (is_numeric($value)) {
                $preparedArguments[] = $value;
            } elseif ($value === null) {
                $preparedArguments[] = 'NULL';
            } else {
                $preparedArguments[] = '$injectedArguments[' . $index . ']';
                $injectedArguments[$index] = $value;
            }
        }
    }

    /**
     * Returns the component configuration from cache or fetches it from scratch.
     *
     * @param string $componentName
     *
     * @return mixed
     * @throws \Exception
     */
    protected function getComponentConfiguration($componentName)
    {
        $componentNameParts = explode('_', $componentName, 3);
        if (($componentNameParts[0] !== self::PACKAGE_PREFIX) || !isset($componentNameParts[1])) {
            throw new Exception('Invalid component name. Component name "' . $componentName . '" must be prefixed by "' . self::PACKAGE_PREFIX . '_".');
        }
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.'][self::PACKAGE_PREFIX . '_' . $componentNameParts[1] . '.']['components.'][$componentName . '.'])) {
            $componentConfiguration = $GLOBALS['TSFE']->tmpl->setup['plugin.'][self::PACKAGE_PREFIX . '_' . $componentNameParts[1] . '.']['components.'][$componentName . '.'];
        } elseif (isset($GLOBALS['TSFE']->tmpl->setup['plugin.'][self::PACKAGE_PREFIX . '_' . ucfirst(self::THIS_PACKAGE_KEY) . '.']['components.'][$componentName . '.'])) {
            $componentConfiguration = $GLOBALS['TSFE']->tmpl->setup['plugin.'][self::PACKAGE_PREFIX . '_' . ucfirst(self::THIS_PACKAGE_KEY) . '.']['components.'][$componentName . '.'];
        } else {
            $componentConfiguration = null;
        }
        return $componentConfiguration;
    }

    /**
     * Puts a component object and its configuration to the cache
     *
     * @param string $componentName
     * @param string $componentObject
     * @return void
     */
    protected function putComponentObject($componentName, $componentObject)
    {
        if (is_object($componentObject)) {
            $this->componentObjects[$componentName] = $componentObject;
        }
    }

    /**
     * Tests if a component object already exists in cache
     *
     * @param string $componentName
     *
     * @return bool
     */
    protected function componentObjectExists($componentName)
    {
        if (!is_string($componentName)) {
            throw new InvalidArgumentException('The component name must be of type string, ' . gettype($componentName) . ' given.');
        }
        return array_key_exists($componentName, $this->componentObjects);
    }

    /**
     * Tests if a component configuration already exists in cache
     *
     * @param string $componentName
     *
     * @return bool
     */
    protected function componentConfigurationExists($componentName)
    {
        if (!is_string($componentName)) {
            throw new InvalidArgumentException('The component name must be of type string, ' . gettype($componentName) . ' given.');
        }
        return array_key_exists($componentName, $this->componentConfigurations);
    }

    /**
     * Returns the scope of the specified component. If it is not defined in the component
     * configuration, the scope is 'singleton'.
     *
     * @param string $componentName: Name of the component
     * @param \Tx_FLOW3_Component_Configuration $componentConfiguration: The component configuration
     * @return string The scope
     * @author Robert Lemke <robert@typo3.org>
     * @author adapted for TYPO3v4 by Jochen Rau <jochen.rau@typoplanet.de>
     */
    protected function getComponentScope($componentName, $componentConfiguration)
    {
        $scope = !is_null($componentConfiguration['scope']) ? $componentConfiguration['scope'] : 'prototype';
        return $scope;
    }

    /**
     * Builds and returns an array of class names => file names of all
     * tx_*.php files in the extension's Classes directory and its sub-
     * directories.
     *
     * @param string $packageKey
     * @param string $subDirectory: for recursion
     * @param int $recursionLevel: maximum depth of recursion
     * @return array
     * @author Robert Lemke <robert@typo3.org>
     * @author adapted for TYPO3v4 by Jochen Rau <jochen.rau@typoplanet.de>
     * @throws Exception if recursion into directories was too deep or another error occurred
     */
    protected function buildArrayOfClassFiles($packageKey, $subDirectory = '', $recursionLevel = 0)
    {
        $classFiles = [];
        if (strpos($packageKey, '/') === false) {
            $currentPath = $this->getPackagePath($packageKey) . self::DIRECTORY_CLASSES . $subDirectory;
        } else {
            $currentPath = $this->getPackagePath($packageKey) . $subDirectory;
        }

        if (!is_dir($currentPath)) {
            return [];
        }
        if ($recursionLevel > 100) {
            throw new Exception('Recursion too deep.');
        }

        try {
            $classesDirectoryIterator = new DirectoryIterator($currentPath);
            while ($classesDirectoryIterator->valid()) {
                $filename = $classesDirectoryIterator->getFilename();

                if ($filename{0} != '.') {
                    if (is_dir($currentPath . $filename)) {
                        $classFiles = array_merge($classFiles, $this->buildArrayOfClassFiles($packageKey, $subDirectory . $filename . '/', ($recursionLevel+1)));
                    } else {
                        if (substr($filename, 0, 3) == self::PACKAGE_PREFIX . '_' && substr($filename, -4, 4) == '.php') {
                            $classFiles[substr($filename, 0, -4)] = $currentPath . $filename;
                        }
                    }
                }
                $classesDirectoryIterator->next();
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
        return $classFiles;
    }

    /**
     * Loads php files containing classes or interfaces found in the classes directory of
     * a package and specifically registered classes.
     *
     * @param   string $className: Name of the class/interface to load
     * @return  void
     * @author  Jochen Rau <jochen.rau@typoplanet.de>
     */
    private function loadClass($className)
    {
        $classNameParts = explode('_', $className, 3);
        if ($classNameParts[0] === self::PACKAGE_PREFIX) {

            // Caches the $classFiles
            if ($this->classFiles === null || empty($this->classFiles)) {
                $this->classFiles = $this->buildArrayOfClassFiles($classNameParts[1]);
            }

            if (is_array($this->tsConf['additionalIncludePaths.'])) {
                foreach ($this->tsConf['additionalIncludePaths.'] as $dir) {
                    $temp = [];
                    $temp = $this->buildArrayOfClassFiles($dir);

                    $this->classFiles = array_merge($temp, $this->classFiles);
                }
            }

            $classFilePathAndName = isset($this->classFiles[$className]) ? $this->classFiles[$className] : null;
            if (isset($classFilePathAndName) && file_exists($classFilePathAndName)) {
                require_once($classFilePathAndName);
            }
        }
    }

    protected function getPackagePath($packageKey)
    {
        if (strpos($packageKey, '/') === false) {
            $path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(strtolower($packageKey));
        } else {
            $path = GeneralUtility::getFileAbsFileName($packageKey);
            if (substr($path, strlen($path) -1) !== '/') {
                $path .= '/';
            }
        }

        return $path;
    }
}
