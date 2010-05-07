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

namespace Mutateme\Adapter\Phpunit;

require_once 'PHPUnit/TextUI/Command.php';

\PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

class Runner
{

    /**
     * Uses an instance of PHPUnit_TextUI_Command to execute the PHPUnit
     * tests and simulates any Mutateme supported command line options suitable
     * for PHPUnit. At present, we merely dissect a generic 'options' string
     * equivelant to anything typed into a console after a normal 'phpunit'
     * command. The adapter captures the TextUI output for further processing.
     *
     * @param array $arguments Mutateme arguments to pass to PHPUnit
     * @return void
     */
    public static function main(array $arguments)
    {
        $optionString = 'phpunit';
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
