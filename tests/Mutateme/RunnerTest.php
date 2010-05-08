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

require_once 'Mutateme/Utility/Runkit.php';

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
        $generator = $this->getMock('Mutateme\Generator');
        $runner->setGenerator($generator);
        $this->assertSame($generator, $runner->getGenerator());
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
        $generator = $this->getMock('Mutateme\Generator');
        $generator->expects($this->once())
            ->method('setSourceDirectory')
            ->with($this->equalTo($this->root));
        $runner->setGenerator($generator);
    }

    public function testShouldUseGeneratorToCreateMutablesAndStoreAllForRetrievalUsingGetMutablesMethod()
    {
        $runner = new \Mutateme\Runner;
        $runner->setSourceDirectory($this->root);
        $generator = $this->getMock('Mutateme\Generator');
        $generator->expects($this->once())
            ->method('generate');
        $generator->expects($this->once())
            ->method('getMutables')
            ->will($this->returnValue(array('mut1', 'mut2')));
        $runner->setGenerator($generator);
        $this->assertEquals(array('mut1', 'mut2'), $runner->getMutables());
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
        $adapter = $this->getMockForAbstractClass('Mutateme\Adapter\AdapterAbstract');
        $runner->setAdapter($adapter);
        $this->assertSame($adapter, $runner->getAdapter());
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
        $this->assertTrue($runner->getRunkit() instanceof \Mutateme\Utility\Runkit);
    }

}
