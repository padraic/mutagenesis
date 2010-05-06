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

require_once 'PHPUnit/TextUI/TestRunner.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

namespace Mutateme\Adapter\Phpunit;

class Runner
{

    public static function main(array $arguments)
    {
        $runner = new \PHPUnit_TextUI_TestRunner;

        $suite = $runner->getTest(
            $arguments['test'],
            $arguments['testFile']
        );

        if ($suite->testAt(0) instanceof \PHPUnit_Framework_Warning &&
            strpos($suite->testAt(0)->getMessage(), 'No tests found in class') !== FALSE) {
            require_once 'PHPUnit/Util/Skeleton/Test.php';

            $skeleton = new \PHPUnit_Util_Skeleton_Test(
                $arguments['test'],
                $arguments['testFile']
            );

            $result = $skeleton->generate(TRUE);

            if (!$result['incomplete']) {
                eval(str_replace(array('<?php', '?>'), '', $result['code']));
                $suite = new \PHPUnit_Framework_TestSuite($arguments['test'] . 'Test');
            }
        }

        try {
            $runner->doRun(
              $suite,
              $arguments
            );
        } catch (\Exception $e) {
            throw new \RuntimeException(
              'Could not create and run test suite: ' . $e->getMessage()
            );
        }

    }

}
