<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once 'Mutateme/MutableFileTest.php';
require_once 'Mutateme/GeneratorTest.php';
require_once 'Mutateme/FrameworkTest.php';
require_once 'Mutateme/RunnerTest.php';
require_once 'Mutateme/MutationTest.php';
require_once 'Mutateme/RunkitTest.php';

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mutateme: Mutation Testing For PHP');

        $suite->addTestSuite('Mutateme_MutableFileTest');
        $suite->addTestSuite('Mutateme_GeneratorTest');
        $suite->addTestSuite('Mutateme_FrameworkTest');
        $suite->addTestSuite('Mutateme_RunnerTest');
        $suite->addTestSuite('Mutateme_MutationTest');
        $suite->addTestSuite('Mutateme_RunkitTest');
        //$suite->addTest(new PHPUnit_Extensions_PhptTestSuite(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Mutateme'));

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
