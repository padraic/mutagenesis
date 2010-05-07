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

namespace Mutateme;

class Runner
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
     * Set the base directory of the project being mutated
     *
     * @param string $dir
     */
    public function setBaseDirectory($dir)
    {
        $dir = rtrim($dir, ' \\/');
        if (!is_dir($dir) || !is_readable($dir)) {
            throw new Exception('Invalid base directory: "'.$dir.'"');
        }
        $this->_baseDirectory = $dir;
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
            throw new Exception('Invalid source directory: "'.$dir.'"');
        }
        $this->_sourceDirectory = $dir;
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
            throw new Exception('Invalid test directory: "'.$dir.'"');
        }
        $this->_testDirectory = $dir;
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
     * Execute the runner
     *
     * @return void
     */
    public function execute()
    {
    
    }
    
}
