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

require_once 'Mutateme/Mutation/MutationAbstract.php';

require_once 'Mutateme/Mutation/OperatorAddition.php';

require_once 'Mutateme/Utility/Runkit.php';

class Mutateme_RunkitTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files';
    }
    
    public function testShouldApplyGivenMutationsUsingRunkitToReplaceEffectedMethods()
    {
        $mutation = array(
            'file' => $this->root . '/runkit/Math1.php',
            'class' => 'RunkitTest_Math1',
            'method' => 'add',
            'args' => '$op1,$op2',
            'tokens' => array(array(335,'return',7), array(309,'$op1',7), '+', array(309,'$op2',7), ';'),
            'index' => 2,
            'mutation' => new \Mutateme\Mutation\OperatorAddition($this->root . '/runkit/Math1.php')
        );
        $runkit = new \Mutateme\Utility\Runkit;
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
            'mutation' => new \Mutateme\Mutation\OperatorAddition($this->root . '/runkit/Math1.php')
        );
        $runkit = new \Mutateme\Utility\Runkit;
        $runkit->applyMutation($mutation);
        $math = new RunkitTest_Math1;
        $runkit->reverseMutation($mutation);
        $this->assertEquals(2, $math->add(1,1));
    }

    public function testShouldApplyGivenMutationsUsingRunkitToReplaceEffectedStaticMethods()
    {
        $mutation = array(
            'file' => $this->root . '/runkit/Math2.php',
            'class' => 'RunkitTest_Math2',
            'method' => 'add',
            'args' => '$op1,$op2',
            'tokens' => array(array(335,'return',7), array(309,'$op1',7), '+', array(309,'$op2',7), ';'),
            'index' => 2,
            'mutation' => new \Mutateme\Mutation\OperatorAddition($this->root . '/runkit/Math2.php')
        );
        $runkit = new \Mutateme\Utility\Runkit;
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
            'mutation' => new \Mutateme\Mutation\OperatorAddition($this->root . '/runkit/Math2.php')
        );
        $runkit = new \Mutateme\Utility\Runkit;
        $runkit->applyMutation($mutation);
        $runkit->reverseMutation($mutation);
        $this->assertEquals(2, RunkitTest_Math2::add(1,1));
    }
}

class StubMutatemeMutation1 extends \Mutateme\Mutation\MutationAbstract
{
    public function getMutation(array $tokens, $index){}
}
