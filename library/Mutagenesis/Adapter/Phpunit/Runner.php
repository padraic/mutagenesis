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

namespace Mutagenesis\Adapter\Phpunit;

require_once 'PHPUnit/TextUI/Command.php';

if (class_exists('PHP_CodeCoverage_Filter', true)) {
    \PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__, 'PHPUNIT');
}

class Runner
{

    /**
     * Uses an instance of PHPUnit_TextUI_Command to execute the PHPUnit
     * tests and simulates any Mutagenesis supported command line options suitable
     * for PHPUnit. At present, we merely dissect a generic 'options' string
     * equivelant to anything typed into a console after a normal 'phpunit'
     * command. The adapter captures the TextUI output for further processing.
     *
     * To prevent duplication of output from stdout, PHPUnit is hard
     * configured to write to stderrm(stdin is used in proc_open call)
     *
     * @param array $arguments Mutagenesis arguments to pass to PHPUnit
     * @return void
     */
    public static function main(array $arguments, $useStdout = false)
    {
        $optionString = 'phpunit';
        if($useStdout) {
            $optionString .= '';
        } else {
            $optionString = ' --stderr';
        }
        if (isset($arguments['options'])) {
            $optionString .= ' ' . $arguments['options'];
        }
        $options = explode(' ', $optionString);
        $originalWorkingDirectory = getcwd();
        if (isset($arguments['tests'])) {
            chdir($arguments['tests']);
        }
        $command = new \PHPUnit_TextUI_Command;
        $command->run($options, false);
        chdir($originalWorkingDirectory);
    }

}
