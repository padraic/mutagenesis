<?php

// Test helper
require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

require_once 'Mutateme/Mutation/OperatorAddition.php';

class Mutateme_MutationTest extends PHPUnit_Framework_TestCase
{

    public function testShouldChangeOriginalMethodBodyTokensToAddMutationReturningMethodBodyStringForRunkit()
    {
        $mutableMethod = array(
            'file' => '/path/to/file',
            'tokens' => array(
                array(T_ECHO, 'echo', 1),'+','-'
            )
        );
        $mutation = new Mutateme_Mutation_OperatorAddition($mutableMethod['file']);
        $blockString = $mutation->mutate($mutableMethod['tokens'], 1);
        $this->assertEquals('echo--', $blockString);
    }

    public function testShouldMaintainFilenameForRetrieval()
    {
        $mutableMethod = array(
            'file' => '/path/to/file',
        );
        $mutation = new Mutateme_Mutation_OperatorAddition($mutableMethod['file']);
        $this->assertEquals('/path/to/file', $mutation->getFilename());
    }

}
