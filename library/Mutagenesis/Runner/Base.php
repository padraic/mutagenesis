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

namespace Mutagenesis\Runner;

class Base extends RunnerAbstract
{
    
    /**
     * Execute the runner
     *
     * @return void
     */
    public function execute()
    {
        $renderer = $this->getRenderer();
        echo $renderer->renderOpening();
        

        /**
         * Run the test suite once to verify it is in a passing state before
         * mutations are applied. There's no point mutation testing source
         * code which is already failing its tests since we'd simply get
         * false positives for every single mutation applied later.
         *
         * This stage also logs the test run to XML/JSON since future runs will
         * attempt to run the fastest test cases first (and slowest last)
         * which in all probability should result in faster mutation test runs
         * going forward.
         */
        $result = $this->getAdapter()->runTests($this, true, true);
        echo $renderer->renderPretest($result[0], $result[1]['stdout']);

        /**
         * If the underlying test suite is not passing, we can't continue.
         */
        if ($result[0] === 'timed out' || !$result[0]) {
            return;
        }

        /**
         * Compile an array of test cases ordered by execution time in first run
         * in ascending order (i.e. fastest first). Would have been logged to /tmp
         * or custom cache directory by first run.
         */
        //echo $renderer->renderPretestTimeAnalysisInProgress(); // TODO
        $timeAnalysis = new \Mutagenesis\Utility\TestTimeAnalyser(
            $this->getCacheDirectory() . '/mutagenesis.xml'
        );
        $orderedTestCases = $timeAnalysis->process();
        
        

        $countMutants = 0;
        $countMutantsKilled = 0;
        $countMutantsEscaped = 0;
        $mutantsEscaped = array();
        $mutantsCaptured = array();

        /**
         * Examine all source code files and collect up mutations to apply
         */
        $mutables = $this->getMutables();

        /**
         * Iterate across all mutations. After each, run the test suite and
         * collect data on how tests handled the mutations. We use ext/runkit
         * to dynamically alter included (in-memory) classes on the fly.
         */
        
        foreach ($mutables as $i=>$mutable) {
            $mutations = $mutable->generate()->getMutations();
            foreach ($mutations as $mutation) {

                $result = $this->getAdapter()->runTests(
                    $this,
                    false,
                    false,
                    $mutation,
                    $orderedTestCases
                );

                $job = new \Mutagenesis\Utility\Job;
                $output = \Mutagenesis\Utility\Process::run(
                    $job->generate($mutation, $orderedTestCases), $this->getTimeout()
                );
                /* TODO: Store output for per-mutant results */

                $countMutants++;
                if ($result[0] === 'timed out' || !$result[0]) {
                    $countMutantsKilled++;
                    if ($this->getDetailCaptures()) {
                        $mutation['mutation']->mutate(
                            $mutation['tokens'],
                            $mutation['index']
                        );
                        $mutantsCaptured[] = array($mutation, $result['stderr']);
                    }
                } else if ($result[0] !== 'process failure') {
                    $countMutantsEscaped++;
                    $mutation['mutation']->mutate(
                        $mutation['tokens'],
                        $mutation['index']
                    );
                    $mutantsEscaped[] = $mutation;
                }
                echo $renderer->renderProgressMark($result[0]);
            }
            $mutable->cleanup();
            unset($this->_mutables[$i]);
        }

        /**
         * Render the final report (todo: rework format sometime)
         */
        echo $renderer->renderReport(
            $countMutants,
            $countMutantsKilled,
            $countMutantsEscaped,
            $mutantsEscaped,
            $mutantsCaptured
        );
    }
    
}
