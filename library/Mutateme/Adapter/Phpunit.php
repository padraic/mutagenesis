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

namespace Mutateme\Adapter;

require_once 'Mutateme/Adapter/AdapterAbstract.php';

require_once 'Mutateme/Adapter/Phpunit/Runner.php';

class Phpunit extends AdapterAbstract
{

    /**
     * Execute the Adapter to run the test suite and parse the results
     *
     * @param array $options Options to be used when called the test suite runner
     * @return bool Boolean indicating whether test suite failed or passed
     */
    public function execute(array $options)
    {
        \Mutateme\Adapter\Phpunit\Runner::main($options);
    }

    /**
     * Parse the PHPUnit text result output to see if there were any failures.
     * In the context of mutation testing, a test failure is good (i.e. the
     * mutation was detected by the test suite).
     *
     * @param string $output
     * @return bool
     */
    public function processOutput($output)
    {
        $lines = explode("\n", $output);
        $useful = array_slice($lines, 2);
        foreach ($useful as $line) {
            if ($line == "\n") {
                break;
            }
            if (preg_match("/.*[EF].*/", $line)) {
                return false;
            }
        }
        return true;
    } 
    
}
