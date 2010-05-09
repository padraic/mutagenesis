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

namespace Mutateme\Runner;

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
         */
        $result = $this->getAdapter()->execute($this->getOptions());
        echo $renderer->renderPretest($result, $this->getAdapter()->getOutput());
        
        /**
         * If the underlying test suite is not passing, we can't continue.
         */
        if (!$result) {
            return;
        }

        $countMutants = 0;
        $countMutantsKilled = 0;
        $countMutantsEscaped = 0;
        $diffMutantsEscaped = array();

        /**
         * Examine all source code files and collect up mutations to apply
         */
        $mutables = $this->getMutables();

        /**
         * Iterate across all mutations. After each, run the test suite and
         * collect data on how tests handled the mutations. We use ext/runkit
         * to dynamically alter included (in-memory) classes on the fly.
         */
        $job = new \Mutateme\Utility\Job($this);
        foreach ($mutables as $mutable) {
            $mutations = $mutable->getMutations();
            foreach ($mutations as $mutation) {
                //---- Here's where the sep process occurs
                $output = \Mutateme\Utility\Process::run(
                    $job->generate($mutation)
                );
                $result = $this->getAdapter()->processOutput($output['stdout']);     /* TODO: Store output for per-mutant results */
                //----
                $countMutants++;
                if (!$result) {
                    $countMutantsKilled++;
                } else {
                    $countMutantsEscaped++;
                    $diffMutantsEscaped[] = $mutation['mutation']->getDiff();
                }
                echo $renderer->renderProgressMark();
            }
        }

        /**
         * Render the final report (todo: rework format some time)
         */
        echo $renderer->renderReport(
            $countMutants,
            $countMutantsKilled,
            $countMutantsEscaped,
            $diffMutantsEscaped,
            $output['stdout']
        );
    }
    
}
