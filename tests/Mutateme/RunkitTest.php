<?php

// Test helper
require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

require_once 'Mutateme/Runkit.php';

require_once 'Mutateme/Mutation.php';

class Mutateme_RunkitTest extends PHPUnit_Framework_TestCase
{
    protected $root = '';

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files';
    }

    // public methods

    public function testShouldApplyGivenMutationsUsingRunkitToReplaceEffectedMethods()
    {
        $mutation = array(
            'file' => $this->root . '/runkit/Math1.php',
            'class' => 'RunkitTest_Math1',
            'method' => 'add',
            'args' => '$op1,$op2',
            'tokens' => array(array(335,'return',7), array(309,'$op1',7), '+', array(309,'$op2',7), ';'),
            'index' => 2,
            'mutation' => new Mutateme_Mutation_OperatorAddition($this->root . '/runkit/Math1.php')
        );
        $runkit = new Mutateme_Runkit;
        $runkit->applyMutation($mutation);
        $math = new RunkitTest_Math1;
        $this->assertEquals(0, $math->add(1,1));
        $runkit->reverseMutation($mutation);
    }

    public function testShouldRevertToOriginalMethodBodyWhenRequested()
    {
        $mutation = array(
            'file' => $this->root . '/runkit/Math1.php',
            'class' => 'RunkitTest_Math1',
            'method' => 'add',
            'args' => '$op1,$op2',
            'tokens' => array(array(335,'return',7), array(309,'$op1',7), '+', array(309,'$op2',7), ';'),
            'index' => 2,
            'mutation' => new Mutateme_Mutation_OperatorAddition($this->root . '/runkit/Math1.php')
        );
        $runkit = new Mutateme_Runkit;
        $runkit->applyMutation($mutation);
        $math = new RunkitTest_Math1;
        $runkit->reverseMutation($mutation);
        $this->assertEquals(2, $math->add(1,1));
    }

    // public static methods

    public function testShouldApplyGivenMutationsUsingRunkitToReplaceEffectedStaticMethods()
    {
        $mutation = array(
            'file' => $this->root . '/runkit/Math2.php',
            'class' => 'RunkitTest_Math2',
            'method' => 'add',
            'args' => '$op1,$op2',
            'tokens' => array(array(335,'return',7), array(309,'$op1',7), '+', array(309,'$op2',7), ';'),
            'index' => 2,
            'mutation' => new Mutateme_Mutation_OperatorAddition($this->root . '/runkit/Math2.php')
        );
        $runkit = new Mutateme_Runkit;
        $runkit->applyMutation($mutation);
        $this->assertEquals(0, RunkitTest_Math2::add(1,1));
        $runkit->reverseMutation($mutation);
    }

    public function testShouldRevertToOriginalStaticMethodBodyWhenRequested()
    {
        $mutation = array(
            'file' => $this->root . '/runkit/Math2.php',
            'class' => 'RunkitTest_Math2',
            'method' => 'add',
            'args' => '$op1,$op2',
            'tokens' => array(array(335,'return',7), array(309,'$op1',7), '+', array(309,'$op2',7), ';'),
            'index' => 2,
            'mutation' => new Mutateme_Mutation_OperatorAddition($this->root . '/runkit/Math2.php')
        );
        $runkit = new Mutateme_Runkit;
        $runkit->applyMutation($mutation);
        $runkit->reverseMutation($mutation);
        $this->assertEquals(2, RunkitTest_Math2::add(1,1));
    }
}

class StubMutatemeMutation1 extends Mutateme_Mutation
{
    public function getMutation(array $tokens, $index){}
}
