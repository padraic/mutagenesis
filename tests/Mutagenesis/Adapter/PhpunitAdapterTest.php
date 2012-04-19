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

    protected $bootstrap = null;

    public function setUp()
    {
        $this->root = dirname(__FILE__) . '/_files';
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
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'src' => dirname(__FILE__) . '/_files/phpunit',
                'tests' => dirname(__FILE__) . '/_files/phpunit',
                'base' => dirname(__FILE__) . '/_files/phpunit',
                'cache' => sys_get_temp_dir(),
                'clioptions' => array(),
                'constraint' => 'MM1_MathTest MathTest.php'
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true
        );
        $this->assertStringStartsWith(
            \PHPUnit_Runner_Version::getVersionString(),
            $result[1]['stdout']
        );
    }

    public function testAdapterRunsPhpunitCommandWithAlltestsFileTarget()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'src' => dirname(__FILE__) . '/_files/phpunit2',
                'tests' => dirname(__FILE__) . '/_files/phpunit2',
                'base' => dirname(__FILE__) . '/_files/phpunit2',
                'cache' => sys_get_temp_dir(),
                'clioptions' => array(),
                'constraint' => 'AllTests.php'
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true
        );
        $this->assertStringStartsWith(
            \PHPUnit_Runner_Version::getVersionString(),
            $result[1]['stdout']
        );
    }

    public function testAdapterDetectsTestsPassing()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => 'PassTest'
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true
        );
        $this->assertTrue($adapter->processOutput($result[1]['stdout']));
    }

    public function testAdapterDetectsTestsFailingFromTestFail()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => 'FailTest'
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true
        );
        $this->assertFalse($adapter->processOutput($result[1]['stdout']));
    }

    public function testAdapterDetectsTestsFailingFromException()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => 'ExceptionTest'
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true
        );
        $this->assertFalse($adapter->processOutput($result[1]['stdout']));
    }

    public function testAdapterDetectsTestsFailingFromError()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => 'ErrorTest'
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true
        );
        $this->assertFalse($adapter->processOutput($result[1]['stdout']));
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
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => ''
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true,
            array(),
            array(
                array(
                    'class' => 'PassTest',
                    'file' => 'PassTest.php'
                ),
                array(
                    'class' => 'FailTest',
                    'file' => 'FailTest.php'
                )
            )
        );
        $this->assertFalse($adapter->processOutput($result[1]['stdout']));
    }

    public function testAdapterDetectsErrorOverMultipleTestCaseRuns()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => ''
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true,
            array(),
            array(
                array(
                    'class' => 'PassTest',
                    'file' => 'PassTest.php'
                ),
                array(
                    'class' => 'ErrorTest',
                    'file' => 'ErrorTest.php'
                )
            )
        );
        $this->assertFalse($adapter->processOutput($result[1]['stdout']));
    }

    public function testAdapterDetectsExceptionOverMultipleTestCaseRuns()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => ''
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true,
            array(),
            array(
                array(
                    'class' => 'PassTest',
                    'file' => 'PassTest.php'
                ),
                array(
                    'class' => 'ExceptionTest',
                    'file' => 'ExceptionTest.php'
                )
            )
        );
        $this->assertFalse($adapter->processOutput($result[1]['stdout']));
    }

    public function testAdapterDetectsPassOverMultipleTestCaseRuns()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => ''
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true,
            array(),
            array(
                array(
                    'class' => 'PassTest',
                    'file' => 'PassTest.php'
                ),
                array(
                    'class' => 'PassTest',
                    'file' => 'PassTest.php'
                )
            )
        );
        $this->assertTrue($adapter->processOutput($result[1]['stdout']));
    }

    public function testAdapterDetectsFailedRun()
    {
        $runner = m::mock('\Mutagenesis\Runner\Base');
        $runner->shouldReceive('getOptions')->andReturn(
            array(
                'tests' => $this->root,
                'clioptions' => array(),
                'cache' => sys_get_temp_dir(),
                'constraint' => ''
            )
        );
        $runner->shouldReceive(array(
            'getBootstrap' => null,
            'getTimeout' => 1200
        ));
        $adapter = new \Mutagenesis\Adapter\Phpunit;
        $result = $adapter->runTests(
            $runner,
            true, 
            true,
            array(),
            array(
                array(
                    'class' => 'PassTest',
                    'file' => 'SyntaxError.php'
                ),
            )
        );
        $this->assertEquals(\Mutagenesis\Adapter\Phpunit::PROCESS_FAILURE, $adapter->processOutput($result[1]['stdout']));
    }
}
