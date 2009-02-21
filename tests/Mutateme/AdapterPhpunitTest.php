<?php

// Test helper
require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

require_once 'Mutateme/Adapter/Phpunit.php';

class Mutateme_AdapterPhpunitTest extends PHPUnit_Framework_TestCase
{

    protected $root = '';

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files/phpunit';
    }

    /**
     * Note: Since MutateMe is tested using PHPUnit - we can't directly execute PHPUnit as part of a test.
     * As a result, please see the related PHPT tests which can operate outside the current PHP process.
     */

    public function testShouldExecutePhpunitUsingGivenClassAndFile()
    {
        $adapter = new Mutateme_Adapter_Phpunit;
        $options = array('test'=>'Mutateme_Test_AllTests', 'testFile' => $this->root.'/AllTests.php');
        $adapter->execute($options);
        $this->assertEquals('PHPUnit 3.3.14 by Sebastian Bergmann.\n\n.\n\nTime: 0 seconds\n\nOK (1 test, 1 assertion)', $adapter->getOutput());
    }
}
