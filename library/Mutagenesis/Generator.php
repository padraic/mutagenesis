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

namespace Mutagenesis;

class Generator
{

    /**
     * Collated files against which mutations can be generated
     *
     * @var array
     */
    protected $_files = array();

    /**
     * Path to the source directory of the project being mutated
     *
     * @var string
     */
    protected $_sourceDirectory = '';

    /**
     * The collection of possible mutations stored as sets of mutation
     * instructions (allowing us to apply and reverse mutations on the fly)
     *
     * @var \Mutagenesis\Mutable[]
     */
    protected $_mutables = array();

    /**
     * Given a source directory (@see \Mutagenesis\Generator::setSourceDirectory)
     * pass each to a \Mutagenesis\Mutable instance which is used to generate
     * mutations and store the instructions for applying and reversing them as
     * a set of mutables (instances of \Mutagenesis\Mutation).
     *
     * @return void
     */
    public function generate($mutableObject = null)
    {
        $files = $this->getFiles();
        foreach ($files as $file) {
            if (is_null($mutableObject)) {
                $mutable = new \Mutagenesis\Mutable($file);
            } else {
                $mutable = new $mutableObject;
                $mutable->setFilename($file);
            }
            $this->_mutables[] = $mutable;
        }
    }

    /**
     * Return an array of mutable files.
     * 
     * @return \Mutagenesis\Mutable[]
     */
    public function getMutables()
    {
        return $this->_mutables;
    }

    /**
     * Set the source directory of the source code to be mutated
     *
     * @param string $sourceDirectory
     */
    public function setSourceDirectory($sourceDirectory)
    {
        if (!is_dir($sourceDirectory) || !is_readable($sourceDirectory)) {
            throw new \Mutagenesis\FUTException('Invalid source directory: "'.$sourceDirectory.'"');
        }
        $this->_sourceDirectory = $sourceDirectory;
    }

    /**
     * Get the source directory of the source code to be mutated
     *
     * @return string
     */
    public function getSourceDirectory()
    {
        return $this->_sourceDirectory;
    }

    /**
     * Return collated files against which mutations can be generated.
     *
     * @return array
     */
    public function getFiles()
    {
        if (empty($this->_files)) {
            if ($this->getSourceDirectory() == '') {
                throw new \Exception('Source directory has not been set');
            }
            $this->_collateFiles($this->getSourceDirectory());
        }
        return $this->_files;
    }

    /**
     * Collate all files capable of being mutated. For now, this only
     * considers files ending in the PHP extension.
     *
     * @return void
     */
    protected function _collateFiles($target)
    {
        $d = dir($target);
        while (FALSE !== ($res = $d->read())) {
            if ($res == '.' || $res == '..') {
                continue;
            }
            $entry = $target . '/' . $res;
            if (is_dir($entry)) {
                $this->_collateFiles($entry);
                continue;
            } elseif (!preg_match("/\.php$/", $res)) { // TODO expand! INC/PHTML/etc.
                continue;
            }
            $this->_files[] = $entry;
        }
        $d->close();
    }
    
}
