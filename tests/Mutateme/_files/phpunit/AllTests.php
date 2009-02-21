<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Mutateme_Test_AllTests::main');
}

require_once dirname(__FILE__).'/MathTest.php';

class Mutateme_Test_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Math');

        $suite->addTestSuite('MathTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Mutateme_Test_AllTests::main') {
    Mutateme_Test_AllTests::main();
}
