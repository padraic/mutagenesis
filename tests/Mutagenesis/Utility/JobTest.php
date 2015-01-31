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
        $job = new \Mutagenesis\Utility\Job;
        $source = '
        $obj = new stdClass;
        $obj->dave = function() {
            return $dave = 123;
        };
        ';
        $script = $job->generate(array('a', '1', $source));
        $autoload = realpath(__DIR__."/../../../vendor/.composer/autoload.php");
        $expected = <<<EXPECTED
<?php

namespace MutagenesisEnv;

declare(ticks = 1);
require_once 'PHPUnit/Autoload.php';
include "$autoload";
class Job {
    static function main () {
        \Mutagenesis\Adapter\Phpunit::main(
            "a:0:{}",
            'a:3:{i:0;s:1:"a";i:1;s:1:"1";i:2;s:115:"
        \$obj = new stdClass;
        \$obj->dave = function() {
            return \$dave = 123;
        };
        ";}',
            null
        );
    }
    static function timeout() {
        throw new \\Exception('Timed Out');
    }
}
pcntl_signal(SIGALRM, array('\\MutagenesisEnv\\Job', 'timeout'), TRUE);
pcntl_alarm(60);
try {
    Job::main();
} catch (\\Exception \$e) {
    pcntl_alarm(0);
    throw \$e;
}
pcntl_alarm(0);
EXPECTED;
        $this->assertEquals($expected, $script);
    }
   
}
