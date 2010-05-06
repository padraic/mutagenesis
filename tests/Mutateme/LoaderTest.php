<?php

class Mutateme_LoaderTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        spl_autoload_unregister('\Mutateme\Loader::loadClass');
    }

    public function testCallingRegisterRegistersSelfAsSplAutoloaderFunction()
    {
        //if (class_exists('Mutateme_Framework', false)) {
        //    $this->markTestSkipped('Cannot run this test if MutateMe is in use for another reason (e.g. self mutation testing!)');
        //}
        require_once 'Mutateme/Loader.php';
        $loader = new \Mutateme\Loader;
        $loader->register();
        $expected = array($loader, 'loadClass');
        $this->assertTrue(in_array($expected, spl_autoload_functions()));
    }

    public function tearDown()
    {
        spl_autoload_unregister('\Mutateme\Loader::loadClass');
    }

}
