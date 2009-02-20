<?php

require_once 'Mutateme/Adapter.php';

class Mutateme_Adapter_Phpunit extends Mutateme_Adapter
{

    protected $_command = 'phpunit AllTests.php';

    public function execute()
    {
        require 'PHPUnit/TextUI/Command.php';
        define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');
        PHPUnit_TextUI_Command::main();
    }

    /**public function execute()
    {
        $currentCWD = getcwd();
        chdir($this->getRunner()->getSpecDirectory());
        $this->setOutput(shell_exec($this->_command));
        chdir($currentCWD);
        return $this->_processOutput($this->getOutput());
    }*/

    protected function _processOutput($output)
    {
        $lines = explode("\n", $output);
        if (preg_match("/.*[EF].*/", $lines[0])) {
            return false;
        }
        return true;
    }
}
