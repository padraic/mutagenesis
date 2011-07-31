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

require_once 'Mutagenesis/Utility/Job.php';

class Mutagenesis_JobTest extends PHPUnit_Framework_TestCase
{

    public function testGenerateReturnsPHPScriptRenderedWithCurrentRunnersSettingsAndSerialisedMutationArray()
    {
        $root = dirname(dirname(__FILE__)) . '/_files/root/base1';
        $src = $root . '/library';
        $tests = $root . '/tests';
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setBaseDirectory($root)
            ->setSourceDirectory($src)
            ->setTestDirectory($tests)
            ->setAdapterName('phpspec')
            ->setAdapterOption('--foo=bar');
        $job = new \Mutagenesis\Utility\Job($runner);
        $script = $job->generate(array('a', '1', new stdClass));
        $expected = <<<EXPECTED
<?php
require_once 'Mutagenesis/Loader.php';
\$loader = new \Mutagenesis\Loader;
\$loader->register();
\$runner = new \Mutagenesis\Runner\Mutation;
\$runner->setBaseDirectory('{$root}')
    ->setSourceDirectory('{$src}')
    ->setTestDirectory('{$tests}')
    ->setCacheDirectory('/tmp')
    ->setAdapterName('phpspec')
    ->setAdapterOptions('a:1:{i:0;s:9:"--foo=bar";}')
    ->setTimeout('120')
    ->setBootstrap('')
    ->setAdapterConstraint('')
    ->setMutation('a:3:{i:0;s:1:"a";i:1;s:1:"1";i:2;O:8:"stdClass":0:{}}')
    ->setTestCasesInExecutionOrder(a:0:{});
\$runner->execute();
EXPECTED;
        $this->assertEquals($expected, $script);
    }

    public function testGenerateReturnsPHPScriptRenderedWithCurrentRunnersSettingsAndLoggingEnabledIfFirstRun()
    {
        $root = dirname(dirname(__FILE__)) . '/_files/root/base1';
        $src = $root . '/library';
        $tests = $root . '/tests';
        $runner = new \Mutagenesis\Runner\Base;
        $runner->setBaseDirectory($root)
            ->setSourceDirectory($src)
            ->setTestDirectory($tests)
            ->setAdapterName('phpspec')
            ->setAdapterOption('--foo=bar');
        $job = new \Mutagenesis\Utility\Job($runner);
        $script = $job->generate(array(), true);
        $tmp = sys_get_temp_dir();
        $expected = <<<EXPECTED
<?php
require_once 'Mutagenesis/Loader.php';
\$loader = new \Mutagenesis\Loader;
\$loader->register();
\$runner = new \Mutagenesis\Runner\Mutation;
\$runner->setBaseDirectory('{$root}')
    ->setSourceDirectory('{$src}')
    ->setTestDirectory('{$tests}')
    ->setCacheDirectory('/tmp')
    ->setAdapterName('phpspec')
    ->setAdapterOptions('a:1:{i:0;s:9:"--foo=bar";}')
    ->setTimeout('120')
    ->setBootstrap('')
    ->setAdapterConstraint('')
    ->setMutation('a:0:{}')
    ->setTestCasesInExecutionOrder(a:0:{});
\$runner->execute(1);
EXPECTED;
        $this->assertEquals($expected, $script);
    }
   
}
