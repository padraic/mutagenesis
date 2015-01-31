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

class Mutagenesis_ProcTest extends PHPUnit_Framework_TestCase
{

    /**
     * @group separateprocess
     */
    public function testExecutesGivenScriptInSeparateProcess()
    {
        $process = new \Mutagenesis\Utility\Process;
        $return = $process->run(
            "<?php echo 'It\'s alive!';"
        );
        $this->assertEquals('It\'s alive!', $return['stdout']);
    }

    /**
     * @group separateprocess
     */
    public function testSeparateProcessCompletesPreTimeout()
    {
        $process = new \Mutagenesis\Utility\Process;
        $autoload = realpath(__DIR__."/../../../vendor/.composer/autoload.php");
        $timeout = 120;
        $job = <<<JOB
<?php
namespace MutagenesisEnv;
declare(ticks = 1);
require_once 'PHPUnit/Autoload.php';
include "$autoload";
class Job {
    static function main () {
        sleep(1);
        echo "MUTAGENESIS-COMPLETE";
    }
    static function timeout() {
        echo "MUTAGENESIS-TIMEOUT";
    }
}
pcntl_signal(SIGALRM, array('\\MutagenesisEnv\\Job', 'timeout'), TRUE);
pcntl_alarm({$timeout});
try {
    Job::main();
} catch (\\Exception \$e) {
    pcntl_alarm(0);
    echo "MUTAGENESIS-TIMEOUT-EXCEPTION";
}
pcntl_alarm(0);
JOB;
        $return = $process->run($job);
        $this->assertEquals('MUTAGENESIS-COMPLETE', $return['stdout']);
    }

    /**
     * @group separateprocess
     */
    public function testSeparateProcessCanTimeout()
    {
        $process = new \Mutagenesis\Utility\Process;
        $timeout = 1;
        $autoload = realpath(__DIR__."/../../../vendor/.composer/autoload.php");
        $job = <<<JOB
<?php
namespace MutagenesisEnv;
declare(ticks = 1);
require_once 'PHPUnit/Autoload.php';
include "$autoload";
class Job {
    static function main () {
        sleep(2);
        return true;
    }
    static function timeout() {
        echo 'MUTAGENESIS-TIMEOUT';
    }
}
pcntl_signal(SIGALRM, array('\\MutagenesisEnv\\Job', 'timeout'), TRUE);
pcntl_alarm(1);
try {
    Job::main();
} catch (\\Exception \$e) {
    pcntl_alarm(0);
    echo "MUTAGENESIS-TIMEOUT-EXCEPTION";
}
pcntl_alarm(0);
JOB;
        $return = $process->run($job);
        $this->assertEquals('MUTAGENESIS-TIMEOUT', $return['stdout']);
    }

    /**
     * @group separateprocess
     */
    public function testSeparateProcessCanThrowExceptions()
    {
        $process = new \Mutagenesis\Utility\Process;
        $timeout = 120;
        $autoload = realpath(__DIR__."/../../../vendor/.composer/autoload.php");
        $job = <<<JOB
<?php
namespace MutagenesisEnv;
declare(ticks = 1);
require_once 'PHPUnit/Autoload.php';
include "$autoload";
class Job {
    static function main () {
        throw new \\Exception();
    }
    static function timeout() {
        echo "MUTAGENESIS-TIMEOUT";
    }
}
pcntl_signal(SIGALRM, array('\\MutagenesisEnv\\Job', 'timeout'), TRUE);
pcntl_alarm({$timeout});
try {
    Job::main();
} catch (\\Exception \$e) {
    pcntl_alarm(0);
    echo "MUTAGENESIS-TIMEOUT-EXCEPTION";
}
pcntl_alarm(0);
JOB;
        $return = $process->run($job);
        $this->assertEquals('MUTAGENESIS-TIMEOUT-EXCEPTION', $return['stdout']);
    }
   
}
