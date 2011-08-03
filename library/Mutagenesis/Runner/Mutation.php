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

namespace Mutagenesis\Runner;

class Mutation extends RunnerAbstract
{

    /**
     * Array containing all data and objects required for applying a mutation
     *
     * @var array
     */
    protected $_mutation = null;

    /**
     * Test Cases to process in a specific execution order
     *
     * @var array
     */
    protected $_testCasesInExecutionOrder = array();
    
    /**
     * Execute the runner. If the initial test run to check all tests are in
     * a passing state, should pass TRUE as first parameter. This will attempt
     * to have the testing framework log execution times of test cases so that
     * subsequent runs run test cases in their order of execution times ascending
     *
     * @param bool $firstRun Indicates if first run of tests
     * @return void
     */
    public function execute($firstRun = false)
    {
        $mutation = $this->getMutation();
        if (!empty($mutation)) {
            if (!is_null($this->getBootstrap())) {
                require_once $this->getBootstrap();
            }
            $this->getRunkit()->applyMutation($mutation);
        }
        $this->getAdapter()->execute($this->getOptions(), false, $firstRun, $this->_testCasesInExecutionOrder);
    }
    
    /**
     * Set a string containing the serialised form a generated mutation and
     * unserialise it into a usable form
     *
     * @param string $mutation Serialized mutation data
     */
    public function setMutation($mutation)
    {
        $this->_mutation = unserialize($mutation);
    }
    
    /**
     * Get the applicable mutation for this run
     *
     * @param string $mutation Serialized mutation data
     */
    public function getMutation()
    {
        return $this->_mutation;
    }

    public function setTestCasesInExecutionOrder($testCases)
    {
        $this->_testCasesInExecutionOrder = unserlialize($testCases);
    }

    public function setAdapterOptions($options)
    {
        parent::setAdapterOptions(unserialize($options));
    }

}


