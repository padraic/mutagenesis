<?php

// Test helper
require_once dirname(dirname(__FILE__)) . '/TestHelper.php';

class Mutateme_FrameworkTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        spl_autoload_unregister(array('Mutateme_Framework','autoload'));
    }

    public function testShouldRegisterSelfAsAutoloadFunctionWhenIncluded()
    {
        require_once 'Mutateme/Framework.php';
        $expected = array('Mutateme_Framework','autoload');
        $this->assertTrue(in_array($expected, spl_autoload_functions()));
    }

    public function tearDown()
    {
        spl_autoload_unregister(array('Mutateme_Framework','autoload'));
    }

}
