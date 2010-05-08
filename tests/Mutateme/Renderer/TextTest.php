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
 
require_once 'Mutateme/Renderer/Text.php';

class Mutateme_Renderer_TextTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->_renderer = new \Mutateme\Renderer\Text;
    }
    
    public function testRendersOpeningMessage()
    {
        $this->assertEquals(
            'MutateMe 0.5: Mutation Testing for PHP' . PHP_EOL . PHP_EOL,
            $this->_renderer->renderOpening()
        );
    }
    
    public function testRendersFailMessageIfTestSuiteDidNotPassDuringPretest()
    {
        $result = false;
        $testOutput = 'Stuff failed';
        $this->assertEquals(
            'Before you face the Mutants, you first need a 100% pass rate!'
                . PHP_EOL
                . 'That means no failures or errors (we\'ll allow skipped or incomplete tests).'
                . PHP_EOL . PHP_EOL
                . $testOutput
                . PHP_EOL . PHP_EOL,
            $this->_renderer->renderPretest($result, $testOutput)
        );
    }
    
    public function testRendersPassMessageIfTestSuiteDidPassDuringPretest()
    {
        $result = true;
        $testOutput = 'Stuff passed';
        $this->assertEquals(
            'All initial checks successful! The mutagenic slime has been activated.'
                . ' Stand by...'
                . PHP_EOL . PHP_EOL
                . $testOutput
                . PHP_EOL,
            $this->_renderer->renderPretest($result, $testOutput)
        );
    }
    
    public function testRendersProgressMarkAsPeriodCharacter()
    {
        $this->assertEquals('.', $this->_renderer->renderProgressMark());
    }
    
    public function testRendersFinalReportWithNoEscapeesFromASingleMutant()
    {
        $this->assertEquals(
            PHP_EOL . PHP_EOL
                . '1 Mutant born out of the mutagenic slime!'
                . PHP_EOL . PHP_EOL
                . 'No Mutants survived! Someone in QA will be happy.'
                . PHP_EOL . PHP_EOL,
            $this->_renderer->renderReport(1, 1, 0, array(), '')
        );   
    }
    
    public function testRendersFinalReportWithEscapeesFromASingleMutant()
    {
        $this->assertEquals(
            PHP_EOL . PHP_EOL
                . '1 Mutant born out of the mutagenic slime!'
                . PHP_EOL . PHP_EOL
                . '1 Mutant escaped; the integrity of your source code may be compromised by the following Mutants:'
                . PHP_EOL . PHP_EOL
                . '1) '
                . PHP_EOL
                . 'diff1'
                . PHP_EOL . PHP_EOL
                . 'test1output'
                . PHP_EOL . PHP_EOL
                . 'Happy Hunting! Remember that some Mutants may just be'
                . ' Ghosts (or if you want to be boring, \'false positives\').'
                . PHP_EOL . PHP_EOL,
            $this->_renderer->renderReport(1, 0, 1, array('diff1'), 'test1output')
        );   
    }

}
