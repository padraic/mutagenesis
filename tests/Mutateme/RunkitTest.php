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

    public function testShouldRequireAnyFileHoldingClassesOnWhichMutationsWillBeApplied()
    {
        $runkit = new Mutateme_Runkit;
        $runkit->applyMutation(new StubMutatemeMutation1($this->root.'/requires/require1.php'));
        $this->assertTrue(class_exists('Require1', false));
    }

    /*public function testShouldApplyGivenMutationsUsingRunkitToReplaceEffectedMethods()
    {
        $file = new Mutateme_MutableFile($this->root . '/runkit/Math1.php');
        $file->generateMutations();
        $mutations = $file->getMutations();
        $runkit = new Mutateme_Runkit;
        var_dump(array_shift($mutations)); exit;
        $runkit->applyMutation(array_shift($mutations));
    }*/
}

class StubMutatemeMutation1 extends Mutateme_Mutation
{
    public function getMutation(array $tokens, $index){}
}
