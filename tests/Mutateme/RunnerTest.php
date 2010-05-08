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

require_once 'Mutateme/Runkit.php';

require_once 'Mutateme/Runner.php';

class Mutateme_RunnerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files/root/base1';
        $this->badRoot = '/path/does/not/exist';
    }

    public function testShouldStoreSourceDirectoryValue()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals($this->root, $runner->getSourceDirectory());
    }

    /**
     * @expectedException Exception
     */
    public function testShouldThrowExceptionOnNonexistingDirectoryWhenSettingSourceDirectory()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->badRoot);
    }

    public function testShouldStoreTestDirectoryValue()
    {
        $runner = new \Mutateme\Runner;
        $runner->setTestDirectory($this->root);
        $this->assertEquals($this->root, $runner->getTestDirectory());
    }

    /**
     * @expectedException Exception
     */
    public function testShouldThrowExceptionOnNonexistingDirectoryWhenSettingTestDirectory()
    {
        $runner = new \Mutateme\Runner;
        $runner->setTestDirectory($this->badRoot);
    }

    public function testShouldStoreAdapterNameValue()
    {
        $runner = new\ Mutateme\Runner;
        $runner->setAdapterName('PHPSpec');
        $this->assertEquals('PHPSpec', $runner->getAdapterName());
    }

    public function testShouldStoreGeneratorObjectIfProvided()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setGenerator(new stubMutatemeGenerator1);
        $this->assertTrue($runner->getGenerator() instanceof stubMutatemeGenerator1);
    }

    public function testShouldCreateGeneratorWhenNeededIfNoneProvided()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertTrue($runner->getGenerator() instanceof \Mutateme\Generator);
    }

    public function testShouldSetGeneratorSourceDirectoryWhenGeneratorCreated()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals($this->root, $runner->getGenerator()->getSourceDirectory());
    }

    public function testShouldSetGeneratorSourceDirectoryWhenGeneratorProvided()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setGenerator(new stubMutatemeGenerator1);
        $this->assertEquals($this->root, $runner->getGenerator()->getSourceDirectory());
    }

    public function testShouldProvideMutablesIfAlreadyAvailable()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setGenerator(new stubMutatemeGenerator1);
        $this->assertEquals(array('mutables'), $runner->getMutables());
    }

    public function testShouldGenerateMutablesWhenRequestedButNotYetAvailable()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals(2, count($runner->getMutables()));
    }

    public function testShouldProvideTestingAdapterIfAlreadyAvailable()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setAdapter(new stubMutatemeAdapter1);
        $this->assertTrue($runner->getAdapter() instanceof stubMutatemeAdapter1);
    }

    public function testShouldCreateTestingAdapterIfNotAlreadyAvailable()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setAdapterName('PHPUNIT');
        $this->assertTrue($runner->getAdapter() instanceof \Mutateme\Adapter\Phpunit);
    }

    /**
     * @expectedException Exception
     */
    public function testShouldThrowExceptionIfAdapterNameGivenIsNotSupported()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setAdapterName('DOESNOTCOMPUTE');
        $runner->getAdapter();
    }

    public function testShouldCreateRunkitWrapperIfNotAvailable()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertTrue($runner->getRunkit() instanceof \Mutateme\Runkit);
    }

}

class stubMutatemeGenerator1 extends \Mutateme\Generator
{
    protected $_mutables = array('mutables');
    public function generate($mutableObject = null) {}
}

class stubMutatemeAdapter1 extends \Mutateme\Adapter\AdapterAbstract
{
    public function execute(array $options = null) {}
}

class stubMutatemeRunkit1 extends \Mutateme\Runkit
{
}
