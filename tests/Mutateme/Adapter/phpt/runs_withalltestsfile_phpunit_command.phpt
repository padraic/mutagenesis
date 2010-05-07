--TEST--
Should Execute Phpunit Using Given Class And File
--FILE--
<?php
require_once 'Mutateme/Adapter/Phpunit.php';
$adapter = new \Mutateme\Adapter\Phpunit;
$options = array(
    'src' => dirname(__FILE__) . '/_files/phpunit2',
    'tests' => dirname(__FILE__) . '/_files/phpunit2',
    'base' => dirname(__FILE__) . '/_files/phpunit2',
    'options' => 'AllTests.php'
);
$adapter->execute($options);
echo $adapter->getOutput();
?>
--EXPECTF--
PHPUnit %s by Sebastian Bergmann.

.

Time: %i seconds, Memory: %i.%iMb

OK (1 test, 1 assertion)
