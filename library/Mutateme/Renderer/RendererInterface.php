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

interface RendererInterface
{

    /**
     * Render the opening message (i.e. app and version mostly)
     *
     * @return string
     */
    public function renderOpening();

    /**
     * Render Mutateme output based on test pass. This is the pretest output,
     * rendered after a first-pass test run to ensure the test suite is in an
     * initial passing state.
     *
     * @param string $result Result state from test adapter
     * @param string $output Result output from test adapter
     * @return string Pretest output to echo to client
     */
    public function renderPretest($result, $output);

    /**
     * Render a progress marker indicating the execution of a single mutation
     * and the successful execution of the related test suite
     *
     * @return string
     */
    public function renderProgressMark();

    /**
     * Render the final MutateMe report
     *
     * @param integer $total Total mutations made and tested
     * @param integer $killed Number of mutations that did cause a test failure
     * @param integer $escaped Number of mutations that did not cause a test failure
     * @param array $mutationDiffs Array of mutation diff strings showing each test-fail mutation
     * @param string $output Result output from test adapter
     * @return string
     */
    public function renderReport($total, $killed, $escaped, array $mutationDiffs, $output);

}
