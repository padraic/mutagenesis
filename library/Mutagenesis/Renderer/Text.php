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

namespace Mutagenesis\Renderer;

class Text implements RendererInterface
{

    /**
     * Render the opening message (i.e. app and version mostly)
     *
     * @return string
     */
    public function renderOpening()
    {
        $out = 'Mutagenesis: Mutation Testing for PHP'
            . PHP_EOL . PHP_EOL;
        return $out;
    }

    /**
     * Render Mutagenesis output based on test pass. This is the pretest output,
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
            $out = 'Before you face the Mutants, you first need a 100% pass rate!'
                . PHP_EOL
                . 'That means no failures or errors (we\'ll allow skipped or incomplete tests).'
                . PHP_EOL . PHP_EOL
                . $output
                . PHP_EOL . PHP_EOL;
            return $out;
        }
        $out = 'All initial checks successful! The mutagenic slime has been activated.'
                . PHP_EOL . PHP_EOL
                . $this->_indentTestOutput($output)
                . PHP_EOL . PHP_EOL
                . 'Stand by...Mutation Testing commencing.'
                . PHP_EOL . PHP_EOL;
        return $out;
    }

    /**
     * Render a progress marker indicating the execution of a single mutation
     * and the successful execution of the related test suite
     *
     * @param bool $result Whether unit tests passed (bad) or not (good)
     * @return string
     */
    public function renderProgressMark($result)
    {
        if ($result === 'timed out') {
            return 'T';
        } elseif ($result === 'process failure') {
            return 'F';
        } elseif ($result) {
            return 'E';
        } else {
            return '.';
        }
    }

    /**
     * Render the final Mutagenesis report
     *
     * @param integer $total Total mutations made and tested
     * @param integer $killed Number of mutations that did cause a test failure
     * @param integer $escaped Number of mutations that did not cause a test failure
     * @param array $mutationDiffs Array of mutation diff strings showing each test-fail mutation
     * @param string $output Result output from test adapter
     * @return string
     */
    public function renderReport($total, $killed, $escaped, array $mutations, array $mutantsCaptured, $output = '')
    {
        $out = PHP_EOL . PHP_EOL
                . $total
                . ($total == 1 ? ' Mutant' : ' Mutants')
                . ' born out of the mutagenic slime!'
                . PHP_EOL . PHP_EOL;
        if ($escaped > 0) {
            $out .= $escaped
                . ($escaped == 1 ? ' Mutant' : ' Mutants')
                . ' escaped; the integrity of your source code may be compromised by the following Mutants:'
                . PHP_EOL . PHP_EOL;
            $i = 1;
            foreach ($mutations as $mutation) {
                $out .= $i . ')'
                    . PHP_EOL
                    . 'Difference on ' . $mutation['class'] . '::' . $mutation['method']
                    . '() in ' . $mutation['file']
                    . PHP_EOL . str_repeat('=', 67) . PHP_EOL
                    . $mutation['mutation']->getDiff()
                    . PHP_EOL;
                if (!empty($output)) {
                    $out .= $this->_indentTestOutput($output)
                        . PHP_EOL . PHP_EOL;
                }
                $i++;
            }
            $out .= 'Happy Hunting! Remember that some Mutants may just be'
            . ' Ghosts (or if you want to be boring, \'false positives\').'
            . PHP_EOL . PHP_EOL;
        } else {
            $out .= 'No Mutants survived! Someone in QA will be happy.'
                . PHP_EOL . PHP_EOL;
        }
        if (count($mutantsCaptured) > 0) {
            $out .= 'The following Mutants were safely captured (see above for escapees):'
                . PHP_EOL . PHP_EOL;
            $i = 1;
            foreach ($mutantsCaptured as $mutant) {
                $out .= $i . ')'
                    . PHP_EOL
                    . 'Difference on ' . $mutant[0]['class'] . '::' . $mutant[0]['method']
                    . '() in ' . $mutant[0]['file']
                    . PHP_EOL . str_repeat('=', 67) . PHP_EOL
                    . $mutant[0]['mutation']->getDiff()
                    . PHP_EOL;
                $out .= 'Reported test output:' . PHP_EOL
                    . PHP_EOL . $this->_indentTestOutput($mutant[1]) . PHP_EOL . PHP_EOL;
                $i++;
            }
            $out .= "Check above for the capture details to see if any mutants"
             . ' escaped.';
        }
        return $out;
    }
    
    /**
     * Utility function to prefix test output lines with an indent and equals sign
     *
     * @var string $output
     * @return string
     */
    protected function _indentTestOutput($output)
    {
        $lines = explode("\n", $output);
        $out = array();
        foreach ($lines as $line) {
            $out[] = '    > ' . $line;
        }
        $return = implode("\n", $out);
        return $return;
    }

}
