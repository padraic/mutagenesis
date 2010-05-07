<?php
/**
 * Mutateme
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
 * @category   Mutateme
 * @package    Mutateme
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/blob/rewrite/LICENSE New BSD License
 */

require_once 'Mutateme/Adapter/Phpunit.php';

class Mutateme_Adapter_PhpunitAdapterTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files';
    }

    public function testAdapterRunsDefaultPhpunitCommand()
    {
        $adapter = new \Mutateme\Adapter\Phpunit;
        $options = array(
            'src' => dirname(__FILE__) . '/_files/phpunit',
            'tests' => dirname(__FILE__) . '/_files/phpunit',
            'base' => dirname(__FILE__) . '/_files/phpunit',
            'options' => 'MM1_MathTest MathTest.php'
        );
        $adapter->execute($options);
        $this->assertStringStartsWith(
            \PHPUnit_Runner_Version::getVersionString(),
            $adapter->getOutput()
        );
    }

    public function testAdapterRunsPhpunitCommandWithAlltestsFileTarget()
    {
        $adapter = new \Mutateme\Adapter\Phpunit;
        $options = array(
            'src' => dirname(__FILE__) . '/_files/phpunit2',
            'tests' => dirname(__FILE__) . '/_files/phpunit2',
            'base' => dirname(__FILE__) . '/_files/phpunit2',
            'options' => 'AllTests.php'
        );
        $adapter->execute($options);
        $this->assertStringStartsWith(
            \PHPUnit_Runner_Version::getVersionString(),
            $adapter->getOutput()
        );
    }

    public function testAdapterDetectsTestsPassing()
    {
        $options = array(
            'tests' => $this->root,
            'options' => 'PassTest'
        );
        $adapter = new \Mutateme\Adapter\Phpunit;
        $this->assertTrue($adapter->execute($options));
    }

    public function testAdapterDetectsTestsFailingFromTestFail()
    {
        $options = array(
            'tests' => $this->root,
            'options' => 'FailTest'
        );
        $adapter = new \Mutateme\Adapter\Phpunit;
        $this->assertFalse($adapter->execute($options));
    }

    public function testAdapterDetectsTestsFailingFromException()
    {
        $options = array(
            'tests' => $this->root,
            'options' => 'ExceptionTest'
        );
        $adapter = new \Mutateme\Adapter\Phpunit;
        $this->assertFalse($adapter->execute($options));
    }

}
