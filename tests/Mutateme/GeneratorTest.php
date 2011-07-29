<?php

require_once 'Mutagenesis/Generator.php';

class Mutagenesis_GeneratorTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files/root/base1';
        $this->badRoot = '/path/does/not/exist';
    }

    public function testShouldStoreSourceDirectoryValue()
    {
        $generator = new \Mutagenesis\Generator;
        $generator->setSourceDirectory($this->root . '/library');
        $this->assertEquals($this->root . '/library', $generator->getSourceDirectory());
    }

    /**
     * @expectedException Exception
     */
    public function testShouldThrowExceptionOnNonexistingDirectory()
    {
        $generator = new \Mutagenesis\Generator;
        $generator->setSourceDirectory($this->badRoot);
    }

    public function testShouldCollateAllFilesValidForMutationTesting()
    {
        $generator = new \Mutagenesis\Generator;
        $generator->setSourceDirectory($this->root);
        $this->assertEquals(array(
            $this->root . '/library/bool2.php',
            $this->root . '/library/bool1.php'
        ),$generator->getFiles());
    }

    public function testShouldGenerateMutableFileObjects()
    {
        $generator = new \Mutagenesis\Generator;
        $generator->setSourceDirectory($this->root);
        $mutable = $this->getMock('Mutable', array('generate', 'setFilename'));
        $generator->generate($mutable);
        $mutables = $generator->getMutables();
        $this->assertTrue($mutables[0] instanceof Mutable);
    }

    public function testShouldGenerateAMutableFileObjectPerDetectedFile()
    {
        $generator = new \Mutagenesis\Generator;
        $generator->setSourceDirectory($this->root);
        $mutable = $this->getMock('Mutable', array('generate', 'setFilename'));
        $generator->generate($mutable);
        $this->assertEquals(2, count($generator->getMutables()));
    }

}
