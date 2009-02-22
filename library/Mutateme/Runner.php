<?php

require_once 'Mutateme/Generator.php';

require_once 'Mutateme/Runkit.php';

require_once 'Mutateme/Adapter/Phpunit.php';

class Mutateme_Runner
{
    protected $_baseDirectory = '';

    protected $_srcDirectory = '';

    protected $_specDirectory = '';

    protected $_adapterName = '';

    protected $_adapter = null;

    protected $_srcFiles = array();

    protected $_mutables = array();

    protected $_generator = null;

    protected $_runkit = null;

    protected $_options = array();

    public function execute()
    {
        // use adapter to ensure all tests are clean
        $options = $this->getOptions();
        $result = $this->getAdapter()->execute($options);
        if (!$result) {
            $str = 'Before you face the Mutants, you first need a 100% pass rate!' . PHP_EOL . PHP_EOL;
            $str .=  $this->getAdapter()->getOutput();
            return $str;
        } else {
            $report = 'All passed!' . PHP_EOL . PHP_EOL;
            $report .=  $this->getAdapter()->getOutput();
        }

        $countMutants = 0;
        $countMutantsKilled = 0;
        $countMutantsEscaped = 0;
        $diffMutantsEscaped = array();

        // process mutants
        $mutables = $this->getMutables();
        foreach ($mutables as $mutable) {
            $mutations = $mutable->getMutations();
            foreach ($mutations as $mutation) {
                $this->getRunkit()->applyMutation($mutation);
                $result = $this->getAdapter()->execute($options);
                $this->getRunkit()->reverseMutation($mutation);
                // result collation
                $countMutants++;
                if ($result) { // careful - we want a FALSE result!
                    $countMutantsKilled++;
                } else { // tests all passing is a BAD thing :)
                    $countMutantsEscaped++;
                    $diffMutantsEscaped[] = $mutation['mutation']->getDiff();
                }
                // small progress echo
                echo '.';
            }
        }

        // reporting
        $report .= $countMutants;
        $report .= $countMutants == 1 ? ' Mutant' : ' Mutants';
        $report .= ' born out of the mutagenic slime!';
        $report .= PHP_EOL . PHP_EOL;
        $report .= $countMutantsKilled;
        $report .= $countMutantsKilled == 1 ? ' Mutant' : ' Mutants';
        $report .= ' exterminated!';
        $report .= PHP_EOL . PHP_EOL;
        if ($countMutantsEscaped > 0) {
            $report .= $countMutantsEscaped;
            $report .= $countMutantsEscaped == 1 ? ' Mutant' : ' Mutants';
            $report .= ' escaped; the integrity of your suite may be compromised by the following Mutants:';
            $report .= PHP_EOL . PHP_EOL;

            $i = 1;
            foreach ($diffMutantsEscaped as $mutantDiff) {
                $report .= $i . ') ' . PHP_EOL . $mutantDiff;
                $report .= PHP_EOL . PHP_EOL;
                $i++;
            }

            $report .= 'Happy Hunting! Remember that some Mutants may just be Ghosts (or if you want to be boring, false positives).';
        } else {
            $report .= 'No Mutants survived! Muahahahaha!';
        }

        return $report;
    }

    public function getFiles()
    {
        if (empty($this->_srcFiles)) {
            $this->_srcFiles = $this->getGenerator()->getFiles();
        }
        return $this->_srcFiles;
    }

    public function setGenerator(Mutateme_Generator $generator)
    {
        $this->_generator = $generator;
        $this->_generator->setSourceDirectory($this->getSourceDirectory());
    }

    public function getGenerator()
    {
        if (!isset($this->_generator)) {
            $this->_generator = new Mutateme_Generator($this);
            $this->_generator->setSourceDirectory($this->getSourceDirectory());
        }
        return $this->_generator;
    }

    public function setSourceDirectory($srcDirectory)
    {
        $srcDirectory = rtrim($srcDirectory, ' \\/');
        if (!is_dir($srcDirectory) || !is_readable($srcDirectory)) {
            throw new Exception('Invalid source directory: "'.$srcDirectory.'"');
        }
        $this->_srcDirectory = $srcDirectory;
    }

    public function getSourceDirectory()
    {
        return $this->_srcDirectory;
    }

    public function setSpecDirectory($specDirectory)
    {
        $specDirectory = rtrim($specDirectory, ' \\/');
        if (!is_dir($specDirectory) || !is_readable($specDirectory)) {
            throw new Exception('Invalid source directory: "'.$specDirectory.'"');
        }
        $this->_specDirectory = $specDirectory;
    }

    public function getSpecDirectory()
    {
        return $this->_specDirectory;
    }

    public function setBaseDirectory($baseDirectory)
    {
        $baseDirectory = rtrim($baseDirectory, ' \\/');
        if (!is_dir($baseDirectory) || !is_readable($baseDirectory)) {
            throw new Exception('Invalid base directory: "'.$baseDirectory.'"');
        }
        $this->_baseDirectory = $baseDirectory;
    }

    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }

    public function setAdapterName($adapter)
    {
        $this->_adapterName = $adapter;
    }

    public function getAdapterName()
    {
        return $this->_adapterName;
    }

    public function getMutables()
    {
        if (empty($this->_mutables)) {
            $generator = $this->getGenerator();
            $generator->generate();
            $this->_mutables = $generator->getMutables();
        }
        return $this->_mutables;
    }

    public function getAdapter()
    {
        if (is_null($this->_adapter)) {
            $name = ucfirst(strtolower($this->getAdapterName()));
            $class = 'Mutateme_Adapter_' . $name;
            if (!class_exists($class)) {
                throw new Exception('Invalid Adapter: ' . strtolower($name));
            }
            $this->_adapter = new $class;
        }
        return $this->_adapter;
    }

    public function setAdapter(Mutateme_Adapter $adapter)
    {
        $this->_adapter = $adapter;
    }

    public function getRunkit()
    {
        if (is_null($this->_runkit)) {
            $this->_runkit = new Mutateme_Runkit;
        }
        return $this->_runkit;
    }

    public function setOption($name, $value)
    {
        $this->_options[$name] = $value;
    }

    public function getOptions()
    {
        $options = array(
            'srcdir' => $this->getSourceDirectory(),
            'specdir' => $this->getSpecDirectory(),
            'basedir' => $this->getBaseDirectory()
        );
        $options = $options + $this->_options;
        return $options;
    }

}




class Mutateme_Runnerx
{

    protected $_sourceDirectory = null;
    protected $_specDirectory = null;
    protected $_adapterName = null;
    protected $_files = array();
    protected $_mutables = null;

    public function execute()
    {
        $this->prepare();

        /****************************************@@
         * Refactor Red Alert For All Code Below :)
         */

        $countMutants = 0;
        $countMutantsKilled = 0;
        $countMutantsEscaped = 0;
        $diffMutantsEscaped = array();

        // MUTANTS!!!
        foreach ($this->_mutables as $mutable) {
            $file = $mutable->getFilename();
            $originalFileContent = file_get_contents($file);
            $mutations = $mutable->getMutations();
            foreach ($mutations as $tokenIndex=>$mutation) {
                $mutatedFileContent = $mutation->mutate($originalFileContent, $tokenIndex);

                //file_put_contents($file, $mutatedFileContent);
                require_once $file;


                $result = $adapter->execute();

                // result collation
                $countMutants++;
                if ($result) { // careful - we want a FALSE result!
                    $countMutantsKilled++;
                } else { // tests all passing is a BAD thing :)
                    $countMutantsEscaped++;
                    $diffMutantsEscaped[] = $mutation->getDiff();
                }

                // revert to original state for next mutation
                file_put_contents($file, $originalFileContent);

                // small progress echo
                echo '.';
            }
        }

        // reporting
        $report = PHP_EOL;
        $report .= $countMutants;
        $report .= $countMutants == 1 ? ' Mutant' : ' Mutants';
        $report .= ' born out of the mutagenic slime!';
        $report .= PHP_EOL . PHP_EOL;
        $report .= $countMutantsKilled;
        $report .= $countMutantsKilled == 1 ? ' Mutant' : ' Mutants';
        $report .= ' exterminated!';
        $report .= PHP_EOL . PHP_EOL;
        if ($countMutantsEscaped > 0) {
            $report .= $countMutantsEscaped;
            $report .= $countMutantsEscaped == 1 ? ' Mutant' : ' Mutants';
            $report .= ' escaped; the integrity of your suite may be compromised by the following Mutants:';
            $report .= PHP_EOL . PHP_EOL;

            $i = 1;
            foreach ($diffMutantsEscaped as $mutantDiff) {
                $report .= $i . ') ' . PHP_EOL . $mutantDiff;
                $report .= PHP_EOL . PHP_EOL;
                $i++;
            }

            $report .= 'Happy Hunting! Remember that some Mutants may just be Ghosts (or if you want to be boring, false positives).';
        } else {
            $report .= 'No Mutants survived! Muahahahaha!';
        }

        return $report;
    }
}
