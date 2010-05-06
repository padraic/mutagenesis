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

namespace Mutateme;

class Runner
{

    /**
     * Path to the base directory of the project being mutated
     *
     * @var string
     */
    protected $_baseDirectory = '';

    /**
     * Set the base directory of the project being mutated
     *
     * @param string $baseDirectory
     */
    public function setBaseDirectory($baseDirectory)
    {
        $baseDirectory = rtrim($baseDirectory, ' \\/');
        if (!is_dir($baseDirectory) || !is_readable($baseDirectory)) {
            throw new Exception('Invalid base directory: "'.$baseDirectory.'"');
        }
        $this->_baseDirectory = $baseDirectory;
    }

    /**
     * Get the base directory of the project being mutated
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }
    
}
