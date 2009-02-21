<?php

// Test helper
require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

require_once 'Mutateme/Runner.php';

class Mutateme_RunnerTest extends PHPUnit_Framework_TestCase
{

    protected $root = '';
    protected $badRoot = '';

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files/mfiles';
        $this->badRoot = dirname(__FILE__) . '/_files/nonexistentdir';
    }

    public function testShouldStoreSourceDirectoryValue()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals($this->root, $runner->getSourceDirectory());
    }

    public function testShouldThrowExceptionOnNonexistingDirectoryWhenSettingSourceDirectory()
    {
        $runner = new Mutateme_Runner;
        try {
            $runner->setSourceDirectory($this->badRoot);
            $this->fail('Failed to detect that given directory does not exist');
        } catch (Exception $e) {
        }
    }

    public function testShouldStoreSpecDirectoryValue()
    {
        $runner = new Mutateme_Runner;
        $runner->setSpecDirectory($this->root);
        $this->assertEquals($this->root, $runner->getSpecDirectory());
    }

    public function testShouldThrowExceptionOnNonexistingDirectoryWhenSettingSpecDirectory()
    {
        $runner = new Mutateme_Runner;
        try {
            $runner->setSpecDirectory($this->badRoot);
            $this->fail('Failed to detect that given directory does not exist');
        } catch (Exception $e) {
        }
    }

    public function testShouldStoreAdapterNameValue()
    {
        $runner = new Mutateme_Runner;
        $runner->setAdapterName('PHPSpec');
        $this->assertEquals('PHPSpec', $runner->getAdapterName());
    }

    public function testShouldStoreGeneratorObjectIfProvided()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setGenerator(new stubMutatemeGenerator1);
        $this->assertTrue($runner->getGenerator() instanceof stubMutatemeGenerator1);
    }

    public function testShouldCreateGeneratorWhenNeededIfNoneProvided()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertTrue($runner->getGenerator() instanceof Mutateme_Generator);
    }

    public function testShouldSetGeneratorSourceDirectoryWhenGeneratorCreated()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals($this->root, $runner->getGenerator()->getSourceDirectory());
    }

    public function testShouldSetGeneratorSourceDirectoryWhenGeneratorProvided()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setGenerator(new stubMutatemeGenerator1);
        $this->assertEquals($this->root, $runner->getGenerator()->getSourceDirectory());
    }

    public function testShouldProvideMutablesIfAlreadyAvailable()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setGenerator(new stubMutatemeGenerator1);
        $this->assertEquals(array('mutables'), $runner->getMutables());
    }

    public function testShouldGenerateMutablesWhenRequestedButNotYetAvailable()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertEquals(2, count($runner->getMutables()));
    }

    public function testShouldProvideTestingAdapterIfAlreadyAvailable()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setAdapter(new stubMutatemeAdapter1);
        $this->assertTrue($runner->getAdapter() instanceof stubMutatemeAdapter1);
    }

    public function testShouldCreateTestingAdapterIfNotAlreadyAvailable()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setAdapterName('PHPUNIT');
        $this->assertTrue($runner->getAdapter() instanceof Mutateme_Adapter_Phpunit);
    }

    public function testShouldThrowExceptionIfAdapterNameGivenIsNotSupported()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $runner->setAdapterName('DOESNOTCOMPUTE');
        try {
            $runner->getAdapter();
            $this->fail('Adapter not available but no Exception was thrown as expected');
        } catch (Exception $e) {
        }
    }

    public function testShouldCreateRunkitWrapperIfNotAvailable()
    {
        $runner = new Mutateme_Runner;
        $runner->setSourceDirectory($this->root);
        $this->assertTrue($runner->getRunkit() instanceof Mutateme_Runkit);
    }

}

class stubMutatemeGenerator1 extends Mutateme_Generator
{
    protected $_mutables = array('mutables');
    public function generate() {}
}

class stubMutatemeAdapter1 extends Mutateme_Adapter
{
    public function execute(array $options = null) {}
}

class stubMutatemeRunkit1 extends Mutateme_Runkit
{
}
