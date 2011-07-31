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

namespace Mutagenesis\Utility;

class Job
{

    /**
     * Set a Runner containing the basic information for setting up
     * a relevant mutation run
     *
     * @var \Mutagenesis\Runner\RunnerAbstract
     */
    protected $_runner = null;
    
    /**
     * Constructor; accepts a starting Runner
     *
     * @param \Mutagenesis\Runner\RunnerAbstract
     */
    public function __construct(\Mutagenesis\Runner\RunnerAbstract $runner)
    {
        $this->_runner = $runner;
    }
    
    /**
     * Generate a new Job script to be executed under a separate PHP process
     *
     * @param array $mutation Mutation data and objects to be used
     * @return string
     */
    public function generate(array $mutation = array(), $firstRun = false, $testCasesInExecutionOrder = array())
    {
        $serializedMutation = serialize($mutation);
        $serializedTestCases = serialize($testCasesInExecutionOrder);
        $serializedOptions = serialize($this->_runner->getAdapterOptions());
        $script = <<<SCRIPT
<?php
require_once 'Mutagenesis/Loader.php';
\$loader = new \Mutagenesis\Loader;
\$loader->register();
\$runner = new \Mutagenesis\Runner\Mutation;
\$runner->setBaseDirectory('{$this->_runner->getBaseDirectory()}')
    ->setSourceDirectory('{$this->_runner->getSourceDirectory()}')
    ->setTestDirectory('{$this->_runner->getTestDirectory()}')
    ->setCacheDirectory('{$this->_runner->getCacheDirectory()}')
    ->setAdapterName('{$this->_runner->getAdapterName()}')
    ->setAdapterOptions('{$serializedOptions}')
    ->setTimeout('{$this->_runner->getTimeout()}')
    ->setBootstrap('{$this->_runner->getBootstrap()}')
    ->setAdapterConstraint('{$this->_runner->getAdapterConstraint()}')
    ->setMutation('{$serializedMutation}')
    ->setTestCasesInExecutionOrder({$serializedTestCases});
\$runner->execute({$firstRun});
SCRIPT;
        return $script;
    }
    
}
