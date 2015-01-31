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

namespace Mutagenesis\Utility;

class TestTimeAnalyser
{

    protected $log = null;

    public function __construct($logFile)
    {
        if (!file_exists($logFile) || !is_readable($logFile)) {
            throw new \Exception('Log file could not be read');
        }
        $this->log = file_get_contents($logFile);
    }

    public function process()
    {
        $testCases = array();
        $time = array();
        $dom = new \DOMDocument;
        $dom->loadXML($this->log);
        $xpath = new \DOMXPath($dom);
        $elements = $xpath->query('//testcase');
        foreach ($elements as $key => $case) {
            if (!isset($testCases[$case->getAttribute('class')])) {
                $testCases[$case->getAttribute('class')] = array(
                    'file' => $case->getAttribute('file'),
                    'time' => 0
                );
            }
            $testCases[$case->getAttribute('class')]['time'] += (float) $case->getAttribute('time');
        }
        unset($xpath);
        foreach ($testCases as $key => $value) {
            $time[$key] = $value['time'];
        }
        array_multisort($time, SORT_ASC, $testCases);
        return $testCases;
    }
    
}
