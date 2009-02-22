--TEST--
Should Execute Phpunit Using Given Class And File
--FILE--
<?php
require_once 'Mutateme/Adapter/Phpunit.php';
$adapter = new Mutateme_Adapter_Phpunit;
$options = array(
    'srcdir' => dirname(__FILE__) . '/_files/phpunit/',
    'specdir' => dirname(__FILE__) . '/_files/phpunit/',
    'basedir' => dirname(__FILE__) . '/_files/phpunit/',
    'test'=>'Mutateme_Test_AllTests',
    'testFile' => dirname(__FILE__) . '/_files/phpunit/AllTests.php'
);
$adapter->execute($options);
echo $adapter->getOutput();
?>
--EXPECTF--
PHPUnit %s by Sebastian Bergmann.

.

Time: %i seconds

OK (1 test, 1 assertion)
