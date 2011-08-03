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

use Mockery as m;

require_once 'Mutagenesis/Adapter/Phpunit.php';

class Mutagenesis_Adapter_PhpunitAdapterTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files';
        $this->runner = m::mock('\Mutagenesis\Runner\Base');
    }

    public function tearDown()
    {
        if (file_exists(sys_get_temp_dir() . '/mutagenesis.xml')) {
            unlink(sys_get_temp_dir() . '/mutagenesis.xml');
        }
        m::close();
    }

    /**
     * @group baserun
     */
    public function testAdapterRunsDefaultPhpunitCommand()
    {
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $this->runner->shouldReceive('getOptions')->andReturn(
            array(
                'src' => dirname(__FILE__) . '/_files/phpunit',
                'tests' => dirname(__FILE__) . '/_files/phpunit',
                'base' => dirname(__FILE__) . '/_files/phpunit',
                'cache' => sys_get_temp_dir(),
                'clioptions' => array(),
                'constraint' => 'MM1_MathTest MathTest.php'
            )
        );
        $this->runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' =>120
        ));
        ob_start();
        $result = $adapter->runTests(
            $this->runner,
            false,
            true
        );
        //var_dump($result); exit("\nend:".__FILE__.__LINE__);
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
            'cache' => sys_get_temp_dir(),
            'clioptions' => array(),
            'constraint' => 'AllTests.php'
        );
        ob_start();
        $adapter->execute($options, true, true);
        $this->assertStringStartsWith(
            \PHPUnit_Runner_Version::getVersionString(),
            ob_get_clean()
        );
    }

    public function testAdapterDetectsTestsPassing()
    {
        $options = array(
            'tests' => $this->root,
            'clioptions' => array(),
            'cache' => sys_get_temp_dir(),
            'constraint' => 'PassTest'
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true, true);
        $this->assertTrue($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsTestsFailingFromTestFail()
    {
        $options = array(
            'tests' => $this->root,
            'clioptions' => array(),
            'cache' => sys_get_temp_dir(),
            'constraint' => 'FailTest'
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true, true);
        $this->assertFalse($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsTestsFailingFromException()
    {
        $options = array(
            'tests' => $this->root,
            'clioptions' => array(),
            'cache' => sys_get_temp_dir(),
            'constraint' => 'ExceptionTest'
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true, true);
        $this->assertFalse($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsTestsFailingFromError()
    {
        $options = array(
            'tests' => $this->root,
            'clioptions' => array(),
            'cache' => sys_get_temp_dir(),
            'constraint' => 'ErrorTest'
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true, true);
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

    public function testAdapterDetectsFailOverMultipleTestCaseRuns()
    {
        $options = array(
            'tests' => $this->root,
            'clioptions' => array(),
            'cache' => sys_get_temp_dir(),
            'constraint' => ''
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true, true, array(
            array(
                'class' => 'PassTest',
                'file' => 'PassTest.php'
            ),
            array(
                'class' => 'FailTest',
                'file' => 'FailTest.php'
            )
        ));
        $this->assertFalse($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsErrorOverMultipleTestCaseRuns()
    {
        $options = array(
            'tests' => $this->root,
            'clioptions' => array(),
            'cache' => sys_get_temp_dir(),
            'constraint' => ''
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true, true, array(
            array(
                'class' => 'PassTest',
                'file' => 'PassTest.php'
            ),
            array(
                'class' => 'ErrorTest',
                'file' => 'ErrorTest.php'
            )
        ));
        $this->assertFalse($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsExceptionOverMultipleTestCaseRuns()
    {
        $options = array(
            'tests' => $this->root,
            'clioptions' => array(),
            'cache' => sys_get_temp_dir(),
            'constraint' => ''
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $adapter->execute($options, true, true, array(
            array(
                'class' => 'PassTest',
                'file' => 'PassTest.php'
            ),
            array(
                'class' => 'ExceptionTest',
                'file' => 'ExceptionTest.php'
            )
        ));
        $this->assertFalse($adapter->processOutput(ob_get_clean()));
    }

    public function testAdapterDetectsPassOverMultipleTestCaseRuns()
    {
        $options = array(
            'tests' => $this->root,
            'clioptions' => array(),
            'cache' => sys_get_temp_dir(),
            'constraint' => ''
        );
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        ob_start();
        $adapter->execute($options, true, true, array(
            array(
                'class' => 'PassTest',
                'file' => 'PassTest.php'
            ),
            array(
                'class' => 'PassTest',
                'file' => 'PassTest.php'
            )
        ));
        $this->assertTrue($adapter->processOutput(ob_get_clean()));
    }

}
