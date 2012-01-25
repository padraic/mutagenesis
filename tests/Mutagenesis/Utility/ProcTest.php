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
        $this->assertEquals('It\'s alive!', $return);
    }

    /**
     * @group separateprocess
     */
    public function testSeparateProcessTimesOut()
    {
        $process = new \Mutagenesis\Utility\Process;
        $return = $process->run(
            "<?php sleep(5);",
            1
        );
        $this->assertEquals('It\'s alive!', $return);
    }
   
}
