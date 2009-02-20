<?php

// Test helper
require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

require_once 'Mutateme/MutableFile.php';

class Mutateme_MutableFileTest extends PHPUnit_Framework_TestCase
{

    protected $root = '';

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files';
    }

    public function testShouldMaintainFilePathInfoOncePassedInConstructor()
    {
        $file = new Mutateme_MutableFile($this->root . '/foo.php');
        $this->assertEquals($this->root . '/foo.php', $file->getFilename());
    }

    public function testShouldNotHaveMutationsBeforeGeneration()
    {
        $file = new Mutateme_MutableFile($this->root . '/math1.php');
        $this->assertEquals(array(), $file->getMutations());
    }

    public function testShouldNotHaveDetectedMutablesBeforeGeneration()
    {
        $file = new Mutateme_MutableFile($this->root . '/math1.php');
        $this->assertEquals(array(), $file->getMutables());
    }

    public function testShouldNotGenerateMutablesForEmptyClass()
    {
        $file = new Mutateme_MutableFile($this->root . '/math0.php');
        $file->generateMutations();
        $this->assertEquals(array(), $file->getMutables());
    }

    public function testShouldNotGenerateMutationsForEmptyClass()
    {
        $file = new Mutateme_MutableFile($this->root . '/math0.php');
        $file->generateMutations();
        $this->assertEquals(array(), $file->getMutations());
    }

    public function testShouldNotGenerateMutablesIfOnlyEmptyMethodsInClass()
    {
        $file = new Mutateme_MutableFile($this->root . '/math00.php');
        $file->generateMutations();
        $this->assertEquals(array(), $file->getMutables());
    }

    public function testShouldNotGenerateMutationsIfOnlyEmptyMethodsInClass()
    {
        $file = new Mutateme_MutableFile($this->root . '/math00.php');
        $file->generateMutations();
        $this->assertEquals(array(), $file->getMutations());
    }

    public function testShouldGenerateMutablesEvenIfMethodBodyIsNotViable()
    {
        $file = new Mutateme_MutableFile($this->root . '/math000.php');
        $file->generateMutations();
        $return = $file->getMutables();
        $this->assertTrue(isset($return['Math']['add']['tokens']));
    }

    public function testShouldNotGenerateMutationsIfMethodBodyIsNotViable()
    {
        $file = new Mutateme_MutableFile($this->root . '/math000.php');
        $file->generateMutations();
        $this->assertEquals(array(), $file->getMutations());
    }

    public function testShouldGenerateAMutationIfPossible()
    {
        $file = new Mutateme_MutableFile($this->root . '/math1.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertTrue(isset($return['Math']['add'][0]['index']));
    }

    public function testShouldReturnMutationsAsMutantObjectWrappers()
    {
        $file = new Mutateme_MutableFile($this->root . '/math1.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertTrue($return['Math']['add'][0]['mutation'] instanceof Mutateme_Mutation);
    }

    public function testShouldDetectMutablesForClassesInSameFileSeparately()
    {
        $file = new Mutateme_MutableFile($this->root . '/mathx2.php');
        $file->generateMutations();
        $return = $file->getMutables();
        $this->assertEquals(array('Math1', 'Math2'), array_keys($return));
    }

    public function testShouldDetectMutationsForClassesInSameFileSeparately()
    {
        $file = new Mutateme_MutableFile($this->root . '/mathx2.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertEquals(array('Math1', 'Math2'), array_keys($return));
    }

    /**
     * Ensure correct class is returned as a mutation
     */

    public function testShouldGenerateAdditionOperatorMutationWhenPlusSignDetected()
    {
        $file = new Mutateme_MutableFile($this->root . '/math1.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertTrue($return['Math']['add'][0]['mutation'] instanceof Mutateme_Mutation_OperatorAddition);
    }

    public function testShouldGenerateSubtractionOperatorMutationWhenMinusSignDetected()
    {
        $file = new Mutateme_MutableFile($this->root . '/math2.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertTrue($return['Math']['subtract'][0]['mutation'] instanceof Mutateme_Mutation_OperatorSubtraction);
    }

    public function testShouldGenerateIncrementOperatorMutationWhenPostIncrementDetected()
    {
        $file = new Mutateme_MutableFile($this->root . '/math3.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertTrue($return['Math']['increment'][0]['mutation'] instanceof Mutateme_Mutation_OperatorIncrement);
    }

    public function testShouldGenerateIncrementOperatorMutationWhenPreIncrementDetected()
    {
        $file = new Mutateme_MutableFile($this->root . '/math4.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertTrue($return['Math']['increment'][0]['mutation'] instanceof Mutateme_Mutation_OperatorIncrement);
    }

    public function testShouldGenerateBooleanTrueMutationWhenBoolTrueDetected()
    {
        $file = new Mutateme_MutableFile($this->root . '/bool1.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertTrue($return['Bool']['boolTrue'][0]['mutation'] instanceof Mutateme_Mutation_BooleanTrue);
    }

    public function testShouldGenerateBooleanFalseMutationWhenBoolFalseDetected()
    {
        $file = new Mutateme_MutableFile($this->root . '/bool2.php');
        $file->generateMutations();
        $return = $file->getMutations();
        $this->assertTrue($return['Bool']['boolFalse'][0]['mutation'] instanceof Mutateme_Mutation_BooleanFalse);
    }

}
