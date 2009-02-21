<?php

require_once 'Mutateme/Adapter.php';

class Mutateme_Adapter_Phpunit extends Mutateme_Adapter
{

    public function execute(array $options = null)
    {
        $old = $_SERVER['argv'];
        $_SERVER['argv'] = array();
        if (isset($options['test'])) {
            $_SERVER['argv'][1] = $options['test'];
        } else {
            $_SERVER['argv'][1] = 'AllTests';
        }
        if (isset($options['testFile'])) {
            $_SERVER['argv'][2] = $options['testFile'];
        } else {
            $_SERVER['argv'][2] = 'AllTests.php';
        }
        ob_start();
        ob_implicit_flush(false);
        require_once 'PHPUnit/TextUI/Command.php';
        PHPUnit_TextUI_Command::main();
        $this->setOutput(ob_get_contents());
        ob_end_clean();
        $_SERVER['argv'] = $old;
        return $this->_processOutput($this->getOutput());
    }

    /**
     * Process output to determine if the specs/tests all passed (return TRUE)
     * or encountered a failure/error (return FALSE)
     *
     * @param string $output Output from the test framework being executed
     * @return bool
     */
    protected function _processOutput($output)
    {
        $lines = explode("\n", $output);
        if (preg_match("/.*[EF].*/", $lines[0])) {
            return false;
        }
        return true;
    }

}
