<?php
require_once('simpletest/autorun.php');
require_once(dirname(__FILE__) . '/math_test.php');

$test = &new GroupTest('All tests');
$test->addTestCase(new TestOfMath());
$test->run(new HtmlReporter());
