<?php
/**
 * Mutagenesis
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mutateme/blob/rewrite/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mutagenesis
 * @package    Mutagenesis
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/blob/rewrite/LICENSE New BSD License
 */

require_once 'Mutagenesis/Adapter/Phpunit.php';

class Mutagenesis_Adapter_PhpunitAdapterTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files';
    }

    public function testAdapterRunsDefaultPhpunitCommand()
    {
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $options = array(
            'src' => dirname(__FILE__) . '/_files/phpunit',
            'tests' => dirname(__FILE__) . '/_files/phpunit',
            'base' => dirname(__FILE__) . '/_files/phpunit',
            'options' => 'MM1_MathTest MathTest.php'
        );
        ob_start();
        $adapter->execute($options, true);
        $this->assertStringStartsWith(
            \PHPUnit_Runner_Version::getVersionString(),
            ob_get_clean()
        );
    }

    public function testAdapterRunsPhpunitCommandWithAlltestsFileTarget()
    {
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $options = array(
            'src' => dirname(__FILE__) . '/_files/phpunit2',
            'tests' => dirname(__FILE__) . '/_files/phpunit2',
            'base' => dirname(__FILE__) . '/_files/phpunit2',
            'options' => 'AllTests.php'
        );
        ob_start();
        $adapter->execute($options, true);
        $this->assertStringStartsWith(
            \PHPUnit_Runner_Version::getVersionString(),
            ob_get_clean()
        );
    }

    public function testAdapterDetectsTestsPassing()
    {
        $options = array(
            'tests' => $this->root,
            'options' => 'PassTest'
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true);
        $this->assertTrue($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsTestsFailingFromTestFail()
    {
        $options = array(
            'tests' => $this->root,
            'options' => 'FailTest'
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true);
        $this->assertFalse($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsTestsFailingFromException()
    {
        $options = array(
            'tests' => $this->root,
            'options' => 'ExceptionTest'
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true);
        $this->assertFalse($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsTestsFailingFromError()
    {
        $options = array(
            'tests' => $this->root,
            'options' => 'ErrorTest'
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true);
        $this->assertFalse($adapter->processOutput(ob_get_clean()));
    }
    
    public function testAdapterOutputProcessingDetectsFailOverMultipleLinesWithNoDepOnFinalStatusReport()
    {
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $output = <<<OUTPUT
PHPUnit 3.4.12 by Sebastian Bergmann.

............................................................ 60 / 300
............................................................ 120 / 300
............................................................ 180 / 300
............................................................ 240 / 300
...........................E................................ 300 / 300

Time: 0 seconds, Memory: 11.00Mb

OK (300 tests, 300 assertions)
OUTPUT;
        $this->assertFalse($adapter->processOutput($output));
    }

}
