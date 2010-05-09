<?php
/**
 * Mutateme
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mutateme/blob/rewrite/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mutateme
 * @package    Mutateme
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/blob/rewrite/LICENSE New BSD License
 */

namespace Mutateme\Runner;

abstract class RunnerAbstract
{

    /**
     * Path to the base directory of the project being mutated
     *
     * @var string
     */
    protected $_baseDirectory = '';

    /**
     * Path to the source directory of the project being mutated
     *
     * @var string
     */
    protected $_sourceDirectory = '';

    /**
     * Path to the tests directory of the project being mutated
     *
     * @var string
     */
    protected $_testDirectory = '';

    /**
     * Name of the test adapter, e.g. phpunit, to utilise
     *
     * @var string
     */
    protected $_adapterName = '';

    /**
     * String of adapter options appended to any call to the current adapter's
     * testing utility command (e.g. passing 'AllTests.php' to phpunit)
     *
     * @var string
     */
    protected $_adapterOptions = '';

    /**
     * Instance of a suitable renderer used to format results into output
     *
     * @var \Mutateme\Renderer\Text
     */
    protected $_renderer = null;
    
    /**
     * Name of the renderer, e.g. text, to utilise
     *
     * @var string
     */
    protected $_rendererName = 'Text';

    /**
     * Instance of \Mutateme\Runkit used to apply and reverse mutations
     * on loaded source code within the same process dynamically
     *
     * @var \Mutateme\Utility\Runkit
     */
    protected $_runkit = null;

    /**
     * Instance of \Mutateme\Generator used to generate mutations from the
     * underlying source code
     *
     * @var \Mutateme\Generator
     */
    protected $_generator = null;

    /**
     * Instance of \Mutateme\Adapter\AdapterAbstract linking Mutateme to
     * to an underlying test framework to execute tests and parse the test
     * results
     *
     * @var \Mutateme\Adapter\AdapterAbstract
     */
    protected $_adapter = null;

    /**
     * Stored mutables each containing relevant mutations for the source code
     * being tested
     *
     * @var array
     */
    protected $_mutables = array();

    /**
     * Generic options, possibly of future use to other test frameworks
     *
     * @var array
     */
    protected $_options = array();
    
    /**
     * Execute the runner
     *
     * @return void
     */
    abstract public function execute();

    /**
     * Set the base directory of the project being mutated
     *
     * @param string $dir
     */
    public function setBaseDirectory($dir)
    {
        $dir = rtrim($dir, ' \\/');
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Exception('Invalid base directory: "'.$dir.'"');
        }
        $this->_baseDirectory = $dir;
        return $this;
    }

    /**
     * Get the base directory of the project being mutated
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }

    /**
     * Set the source directory of the project being mutated
     *
     * @param string $dir
     */
    public function setSourceDirectory($dir)
    {
        $dir = rtrim($dir, ' \\/');
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Exception('Invalid source directory: "'.$dir.'"');
        }
        $this->_sourceDirectory = $dir;
        return $this;
    }

    /**
     * Get the source directory of the project being mutated
     *
     * @return string
     */
    public function getSourceDirectory()
    {
        return $this->_sourceDirectory;
    }

    /**
     * Set the test directory of the project being mutated
     *
     * @param string $dir
     */
    public function setTestDirectory($dir)
    {
        $dir = rtrim($dir, ' \\/');
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new \Exception('Invalid test directory: "'.$dir.'"');
        }
        $this->_testDirectory = $dir;
        return $this;
    }

    /**
     * Get the test directory of the project being mutated
     *
     * @return string
     */
    public function getTestDirectory()
    {
        return $this->_testDirectory;
    }

    /**
     * Set name of the test adapter to use
     *
     * @param string $adapter
     */
    public function setAdapterName($adapter)
    {
        $this->_adapterName = $adapter;
        return $this;
    }

    /**
     * Get name of the test adapter to use
     *
     * @return string
     */
    public function getAdapterName()
    {
        return $this->_adapterName;
    }

    /**
     * Options to pass to adapter's underlying command
     *
     * @param string $optionString
     */
    public function setAdapterOptions($optionString)
    {
        $this->_adapterOptions = $optionString;
        return $this;
    }

    /**
     * Get name of the test adapter to use
     *
     * @return string
     */
    public function getAdapterOptions()
    {
        return $this->_adapterOptions;
    }

    /**
     * Get a test framework adapter. Creates a new one based on the configured
     * adapter name passed on the CLI if not already set.
     *
     * @return \Mutateme\Adapter\AdapterAbstract
     */
    public function getAdapter()
    {
        if (is_null($this->_adapter)) {
            $name = ucfirst(strtolower($this->getAdapterName()));
            $class = 'Mutateme\\Adapter\\' . $name;
            if (!class_exists($class)) {
                throw new \Exception('Invalid Adapter name: ' . strtolower($name));
            }
            $this->_adapter = new $class;
        }
        return $this->_adapter;
    }

    /**
     * Set a test framework adapter.
     *
     * @param \Mutateme\Adapter\AdapterAbstract $adapter
     */
    public function setAdapter(\Mutateme\Adapter\AdapterAbstract $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }
    
    /**
     * Set name of the renderer to use
     *
     * @param string $rname
     */
    public function setRendererName($rname)
    {
        $this->_rendererName = $rname;
        return $this;
    }

    /**
     * Get name of the renderer to use
     *
     * @return string
     */
    public function getRendererName()
    {
        return $this->_rendererName;
    }
    
    /**
     * Get a result renderer. Creates a new one based on the configured
     * renderer name passed on the CLI if not already set.
     *
     * @return \Mutateme\Renderer\RendererInterface
     */
    public function getRenderer()
    {
        if (is_null($this->_renderer)) {
            $name = ucfirst(strtolower($this->getRendererName()));
            $class = 'Mutateme\\Renderer\\' . $name;
            if (!class_exists($class)) {
                throw new \Exception('Invalid Renderer name: ' . strtolower($name));
            }
            $this->_renderer = new $class;
        }
        return $this->_renderer;
    }

    /**
     * Set a test framework adapter.
     *
     * @param \Mutateme\Renderer\RendererInterface $renderer
     */
    public function setRenderer(\Mutateme\Renderer\RendererInterface $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }
    
    /**
     * Set a custom runkit instance.
     *
     * @param \Mutateme\Utility\Runkit $runkit
     */
    public function setRunkit(\Mutateme\Utility\Runkit $runkit)
    {
        $this->_runkit = $runkit;
        return $this;
    }

    /**
     * Creates and returns a new instance of \Mutateme\Runkit if not previously
     * loaded
     *
     * @return \Mutateme\Runkit
     */
    public function getRunkit()
    {
        if (is_null($this->_runkit)) {
            if(!in_array('runkit', get_loaded_extensions())) {
                throw new \Exception(
                    'Runkit extension is not loaded. Unfortunately, runkit'
                    . ' is essential for MutateMe. Please see the manual or'
                    . ' README which explains how to install an updated runkit'
                    . ' extension suitable for MutateMe and PHP 5.3.'
                );
            }
            $this->_runkit = new \Mutateme\Utility\Runkit;
        }
        return $this->_runkit;
    }

    /**
     * Set a generic option
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->_options[$name] = $value;
        return $this;
    }
    
    /**
     * Set generic options
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name=>$value) {
            $this->setOption($name, $value);
        }
        return $this;
    }

    /**
     * Compile all necessary options for the test framework adapter
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array(
            'src' => $this->getSourceDirectory(),
            'tests' => $this->getTestDirectory(),
            'base' => $this->getBaseDirectory(),
            'options' => $this->getAdapterOptions()
        );
        $options = $options + $this->_options;
        return $options;
    }

    /**
     * Generate Mutants!
     *
     * @return array
     */
    public function getMutables()
    {
        if (empty($this->_mutables)) {
            $generator = $this->getGenerator();
            $generator->generate();
            $this->_mutables = $generator->getMutables();
        }
        return $this->_mutables;
    }

    /**
     * Set a specific Generator of mutations (stuck with a subclass).
     * TODO Add interface
     *
     * @param \Mutateme\Generator
     */
    public function setGenerator(\Mutateme\Generator $generator)
    {
        $this->_generator = $generator;
        $this->_generator->setSourceDirectory($this->getSourceDirectory());
        return $this;
    }

    /**
     * Get a specific Generator of mutations.
     *
     * @return \Mutateme\Generator
     */
    public function getGenerator()
    {
        if (!isset($this->_generator)) {
            $this->_generator = new \Mutateme\Generator($this);
            $this->_generator->setSourceDirectory($this->getSourceDirectory());
        }
        return $this->_generator;
    }
    
}
