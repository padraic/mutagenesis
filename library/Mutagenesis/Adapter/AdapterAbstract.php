<?php
/**
 * Mutagenesis
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
 * @category   Mutagenesis
 * @package    Mutagenesis
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/blob/rewrite/LICENSE New BSD License
 */

namespace Mutagenesis\Adapter;

abstract class AdapterAbstract
{

    /**
     * Output from the test library in use
     *
     * @var string
     */
    protected $_output = '';

    /**
     * Execute the Adapter to run the test suite and parse the results
     *
     * @param array $options Options to be used when called the test suite runner
     * @return bool Boolean indicating whether test suite failed or passed
     */
    abstract public function execute(array $options);
    
    /**
     * Parse the result output text to see if there were any failures.
     * In the context of mutation testing, a test failure is good (i.e. the
     * mutation was detected by the test suite).
     *
     * @param string $output
     * @return bool
     */
    abstract public function processOutput($output);

    /**
     * Set the test library output so it can be used later
     *
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->_output = $output;
    }

    /**
     * Get the test library output
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->_output;
    }  
    
}
