<?php

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/PhptTestSuite.php';

class Mutateme_Adapter_PhpunitTest
{

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mutateme Adapter Phpunit');
        $phptTests = new PHPUnit_Extensions_PhptTestSuite(dirname(__FILE__) . '/phpt');
        $suite->addTestSuite($phptTests);
        return $suite;
    }
    
}
