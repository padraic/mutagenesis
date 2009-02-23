<?php

require_once 'Mutateme/MutableFile.php';

class Mutateme_Generator
{

    protected $_files = array();
    protected $_sourceDirectory = '';
    protected $_mutables = array();
    protected $_mutableFileClass = 'Mutateme_MutableFile';

    public function generate()
    {
        $files = $this->getFiles();
        foreach ($files as $file) {
            $mutableFile = new $this->_mutableFileClass($file);
            $mutableFile->generateMutations();
            $this->_mutables[] = $mutableFile;
        }
    }

    /**
     * Return an array of mutable files.
     * 
     * @return Mutateme_MutableFile[]
     */
    public function getMutables()
    {
        return $this->_mutables;
    }

    public function setMutableFileClass($class)
    {
        $this->_mutableFileClass = $class;
    }

    public function setSourceDirectory($sourceDirectory)
    {
        if (!is_dir($sourceDirectory) || !is_readable($sourceDirectory)) {
            throw new Exception('Invalid source directory: "'.$sourceDirectory.'"');
        }
        $this->_sourceDirectory = $sourceDirectory;
    }

    public function getSourceDirectory()
    {
        return $this->_sourceDirectory;
    }

    public function getFiles()
    {
        if (empty($this->_files)) {
            if ($this->_sourceDirectory == '') {
                throw new Exception('Source directory has not been set');
            }
            $this->_collateFiles($this->_sourceDirectory);
        }
        return $this->_files;
    }

    protected function _collateFiles($target)
    {
        $d = dir($target);
        while (FALSE !== ($res = $d->read())) {
            if ($res == '.' || $res == '..') {
                continue;
            }
            if (!preg_match("/\.php$/", $res)) {
                continue;
            }
            $entry = $target . '/' . $res;
            if (is_dir($entry)) {
                $this->_collateFiles($entry);
                continue;
            }
            $this->_files[] = $entry;
        }
        $d->close();
    }

}
