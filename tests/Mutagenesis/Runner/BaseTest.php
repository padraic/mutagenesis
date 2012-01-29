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

require_once 'Mutagenesis/Utility/Runkit.php';

require_once 'Mutagenesis/Runner/RunnerAbstract.php';

require_once 'Mutagenesis/Runner/Base.php';

require_once 'Mutagenesis/Renderer/RendererInterface.php';

class Mutagenesis_RunnerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->root = dirname(dirname(__FILE__)) . '/_files/root/base1';
        $this->badRoot = '/path/does/not/exist';
    }

    public function testShouldStoreSourceDirectoryValue()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals($this->root, $runner->getSourceDirectory());
    }

    /**
     * @expectedException \Mutagenesis\FUTException
     */
    public function testShouldThrowExceptionOnNonexistingDirectoryWhenSettingSourceDirectory()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setSourceDirectory($this->badRoot);
    }

    public function testShouldStoreTestDirectoryValue()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setTestDirectory($this->root);
        $this->assertEquals($this->root, $runner->getTestDirectory());
    }

    /**
     * @expectedException \Mutagenesis\FUTException
     */
    public function testShouldThrowExceptionOnNonexistingDirectoryWhenSettingTestDirectory()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setTestDirectory($this->badRoot);
    }

    public function testShouldStoreAdapterNameValue()
    {
        $runner = new\ Mutagenesis\Runner\Base;
        $runner->setAdapterName('PHPSpec');
        $this->assertEquals('PHPSpec', $runner->getAdapterName());
    }
    
    public function testShouldStoreRendererNameValue()
    {
        $runner = new\ Mutagenesis\Runner\Base;
        $runner->setRendererName('Html');
        $this->assertEquals('Html', $runner->getRendererName());
    }

    public function testShouldStoreGeneratorObjectIfProvided()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setSourceDirectory($this->root);
        $generator = $this->getMock('Mutagenesis\Generator');
        $runner->setGenerator($generator);
        $this->assertSame($generator, $runner->getGenerator());
    }

    public function testShouldCreateGeneratorWhenNeededIfNoneProvided()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setSourceDirectory($this->root);
        $this->assertTrue($runner->getGenerator() instanceof \Mutagenesis\Generator);
    }

    public function testShouldSetGeneratorSourceDirectoryWhenGeneratorCreated()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals($this->root, $runner->getGenerator()->getSourceDirectory());
    }

    public function testShouldSetGeneratorSourceDirectoryWhenGeneratorProvided()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setSourceDirectory($this->root);
        $generator = $this->getMock('Mutagenesis\Generator');
        $generator->expects($this->once())
            ->method('setSourceDirectory')
            ->with($this->equalTo($this->root));
        $runner->setGenerator($generator);
    }

    public function testShouldUseGeneratorToCreateMutablesAndStoreAllForRetrievalUsingGetMutablesMethod()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $generator = $this->getMock('Mutagenesis\Generator');
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
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals(2, count($runner->getMutables()));
    }

    public function testShouldProvideTestingAdapterIfAlreadyAvailable()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $adapter = $this->getMockForAbstractClass('Mutagenesis\Adapter\AdapterAbstract');
        $runner->setAdapter($adapter);
        $this->assertSame($adapter, $runner->getAdapter());
    }
    
    public function testShouldProvideRendererIfAlreadyAvailable()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $renderer = $this->getMock('Mutagenesis\Renderer\RendererInterface');
        $runner->setRenderer($renderer);
        $this->assertSame($renderer, $runner->getRenderer());
    }

    public function testShouldCreateTestingAdapterIfNotAlreadyAvailable()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setAdapterName('PHPUNIT');
        $this->assertTrue($runner->getAdapter() instanceof \Mutagenesis\Adapter\Phpunit);
    }
    
    public function testShouldCreateDefaultTextRendererIfOtherInstanceOrNameNotAlreadyAvailable()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $this->assertTrue($runner->getRenderer() instanceof \Mutagenesis\Renderer\Text);
    }

    /**
     * @expectedException \Mutagenesis\FUTException
     */
    public function testShouldThrowExceptionIfAdapterNameGivenIsNotSupported()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setAdapterName('DOESNOTCOMPUTE');
        $runner->getAdapter();
    }

    public function testShouldCreateRunkitWrapperIfNotAvailable()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setSourceDirectory($this->root);
        $this->assertTrue($runner->getRunkit() instanceof \Mutagenesis\Utility\Runkit);
    }

    public function testShouldStoreCacheDirectoryValue()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setCacheDirectory($this->root);
        $this->assertEquals($this->root, $runner->getCacheDirectory());
    }

    public function testCacheDirectoryDefaultsToTmpIfNotSet()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $this->assertEquals(sys_get_temp_dir(), $runner->getCacheDirectory());
    }

    public function testShouldStoreCliOptions()
    {
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setAdapterOption('foo')->setAdapterOption('bar');
        $this->assertEquals(array('foo', 'bar'), $runner->getAdapterOptions());
    }

}
