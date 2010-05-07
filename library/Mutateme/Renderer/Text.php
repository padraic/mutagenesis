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

namespace Mutateme\Renderer;

class Text
{

    /**
     * Render the opening message (i.e. app and version mostly)
     *
     * @return string
     */
    public function renderOpening()
    {
        $out = 'MutateMe 0.5: Mutation Testing for PHP'
            . PHP_EOL . PHP_EOL;
        return $out;
    }

    /**
     * Render Mutateme output based on test pass. This is the pretest output,
     * rendered after a first-pass test run to ensure the test suite is in an
     * initial passing state.
     *
     * @param string $result Result state from test adapter
     * @param string $output Result output from test adapter
     * @return string Pretest output to echo to client
     */
    public function renderPretest($result, $output)
    {
        if(!$result) {
            $out .= 'Before you face the Mutants, you first need a 100% pass rate!'
                . PHP_EOL
                . 'That means no failures or errors (we\'ll allow skipped or incomplete tests).'
                . PHP_EOL . PHP_EOL
                . $output
                . PHP_EOL . PHP_EOL;
            return $out;
        }
        $out = 'All initial checks successful! The mutagenic slime has been activated.'
                . ' Stand by...'
                . PHP_EOL . PHP_EOL
                . $output
                . PHP_EOL;
        return $out;
    }

    /**
     * Render a progress marker indicating the execution of a single mutation
     * and the successful execution of the related test suite
     *
     * @return string
     */
    public function renderProgressMark()
    {
        return '.';
    }

    /**
     * Render the final MutateMe report
     *
     * @param integer $total Total mutations made and tested
     * @param integer $killed Number of mutations that did not cause a test failure
     * @param integer $escaped Number of mutations that did cause a test failure
     * @param array $mutationDiffs Array of mutation diff strings showing each test-fail mutation
     * @param string $output Result output from test adapter
     * @return string
     */
    public function renderReport($total, $killed, $escaped, array $mutationDiffs, $output)
    {
        $out = PHP_EOL . PHP_EOL
                . $total == 1 ? ' Mutant' : ' Mutants'
                . ' born out of the mutagenic slime!'
                . PHP_EOL . PHP_EOL;
        if ($escaped > 0) {
            $out .= $escaped
                . $escaped == 1 ? ' Mutant' : ' Mutants'
                . ' escaped; the integrity of your source code may be compromised by the following Mutants:'
                . PHP_EOL . PHP_EOL;
            $i = 1;
            foreach ($mutationDiffs as $diff) {
                $out .= $i . ') '
                    . PHP_EOL
                    . $diff
                    . PHP_EOL . PHP_EOL
                    . $output
                    . PHP_EOL . PHP_EOL;
                $i++;
            }
            $out .= 'Happy Hunting! Remember that some Mutants may just be'
            . ' Ghosts (or if you want to be boring, \'false positives\').'
            . PHP_EOL . PHP_EOL;
        } else {
            $out .= 'No Mutants survived! Someone in QA will be happy.'
                . PHP_EOL . PHP_EOL;
        }
        return $out;
    }

}
