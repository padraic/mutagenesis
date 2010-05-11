<?php
/**
 * Mutateme
 *
 * File liberated from Sebastian Bergmann's PHPUnit with minimal editing for
 * MutateMe. Original copyright and license preserved as follows.
 *
 * Copyright (c) 2002-2010, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Mutateme
 * @package    Mutateme
 * @subpackage UnitTests
 * @author     Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright  2002-2010 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 */

namespace Mutateme\Utility;

class Process
{

    /**
     * PHP binary path for the current operating system
     *
     * @var string
     */
    protected static $_phpBin = null;
    
    /**
     * proc_open descriptor spec
     *
     * @var array
     */
    protected static $_descriptorSpec = array(
        0 => array('pipe', 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w')
    );
    
    /**
     * Opens a new process to execute PHP on the source code passed
     * to the method
     *
     * @param string $source
     * @param integer $timeout Time allowed for the process to run before we assume it timed out
     */
    public static function run($source, $timeout = 120)
    {
    var_dump($timeout); exit;
        $process = proc_open(
            self::_getPhpBinary(),
            self::$_descriptorSpec,
            $pipes
        );
        if (is_resource($process)) {
            fwrite($pipes[0], $source);
            fclose($pipes[0]);
            stream_set_timeout($pipes[1], $timeout);
            $stdout = stream_get_contents($pipes[1]);
            $info = stream_get_meta_data($pipes[1]);
            fclose($pipes[1]);
            proc_close($process);
            if($info['timed_out']) {
                return array(
                    'stdout' => 'Your tests timed out. MutateMe may have'
                        . ' inadvertently created an infinite loop using'
                        . ' the current mutation.'
                );
            } else {
                return array(
                    'stdout' => $stdout
                );  
            }
        } else {
            throw new \Exception('Unable to open a new process with proc_open()');
        }
    }
    
    /**
     * Locate a relevant PHP binary for the operating system
     *
     * @return void
     */
    protected static function _getPhpBinary()
    {
        if(!is_null(self::$_phpBin)) {
            return self::$_phpBin;
        }
        if (is_readable('/usr/bin/php')) {
            self::$_phpBin = '/usr/bin/php';
        } elseif (PHP_SAPI == 'cli' && isset($_SERVER['_'])
        && strpos($_SERVER['_'], 'mutateme') !== false) {
            $file = file($_SERVER['_']);
            $tmp = explode(' ', $file[0]);
            self::$_phpBin = trim($tmp[1]);
        }
        if (!is_readable(self::$_phpBin)) {
            self::$_phpBin = 'php';
        } else {
            self::$_phpBin = escapeshellarg(self::$_phpBin);
        }
        return self::$_phpBin;
    }
    
}
