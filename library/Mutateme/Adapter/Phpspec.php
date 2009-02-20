<?php

class PHPMutagenesis_Adapter_Phpspec extends PHPMutagenesis_Adapter
{

    protected $_command = 'phpspec -r';

    public function execute() 
    {
        $currentCWD = getcwd();
        chdir($this->getRunner()->getSpecDirectory());
        $this->setOutput(shell_exec($this->_command));
        chdir($currentCWD);
        return $this->_processOutput($this->getOutput());
    }

    protected function _processOutput($output) 
    {
        $lines = explode("\n", $output);
        if (preg_match("/.*[EF].*/", $lines[0])) {
            return false;
        }
        return true;
    }
}