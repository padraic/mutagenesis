<?php

// Test helper
require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

require_once 'Mutateme/Generator.php';
require_once 'Mutateme/MutableFile.php';

class Mutateme_GeneratorTest extends PHPUnit_Framework_TestCase
{

    protected $root = '';

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files/mfiles';
        $this->badRoot = dirname(__FILE__) . '/_files/nonexistentdir';
    }

    public function testShouldStoreSourceDirectoryValue()
    {
        $generator = new Mutateme_Generator;
        $generator->setSourceDirectory($this->root);
        $this->assertEquals($this->root, $generator->getSourceDirectory());
    }

    public function testShouldThrowExceptionOnNonexistingDirectory()
    {
        $generator = new Mutateme_Generator;
        try {
            $generator->setSourceDirectory($this->badRoot);
            $this->fail('Failed to detect that given directory does not exist');
        } catch (Exception $e) {
        }
    }

    public function testShouldCollateAllFilesValidForMutationTesting()
    {
        $generator = new Mutateme_Generator;
        $generator->setSourceDirectory($this->root);
        $this->assertEquals(array($this->root.'/bool1.php',$this->root.'/bool2.php'), $generator->getFiles());
    }

    public function testShouldGenerateMutableFileObjects()
    {
        $generator = new Mutateme_Generator;
        $generator->setSourceDirectory($this->root);
        $generator->setMutableFileClass('StubMutableFile1');
        $generator->generate();
        $mutables = $generator->getMutables();
        $this->assertTrue($mutables[0] instanceof StubMutableFile1);
    }

    public function testShouldGenerateAMutableFileObjectPerDetectedFile()
    {
        $generator = new Mutateme_Generator;
        $generator->setSourceDirectory($this->root);
        $generator->setMutableFileClass('StubMutableFile1');
        $generator->generate();
        $this->assertEquals(2, count($generator->getMutables()));
    }

}

class StubMutableFile1 extends Mutateme_MutableFile
{
    public function __construct()
    {
    }
    public function generateMutations()
    {
    }
}
