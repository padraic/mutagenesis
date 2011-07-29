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
     * Execute the runner
     *
     * @return void
     */
    public function execute()
    {
        $mutation = $this->getMutation();
        if (!empty($mutation)) {
            if (!is_null($this->getBootstrap())) {
                require_once $this->getBootstrap();
            }
            $this->getRunkit()->applyMutation($mutation);
        }
        $this->getAdapter()->execute($this->getOptions());
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

}


