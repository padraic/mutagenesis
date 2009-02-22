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
            $_SERVER['argv'][2] = $options['specdir'].'/AllTests.php';
        }
        ob_start();
        ob_implicit_flush(false);
        if (!defined('PHPUnit_MAIN_METHOD')) {
            define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::undefined');
        }
        require_once 'PHPUnit/TextUI/Command.php';
        PHPUnit_TextUI_Command::main(false);
        $this->setOutput(ob_get_contents());
        ob_end_clean();
        $_SERVER['argv'] = $old;
        return $this->processOutput($this->getOutput());
    }

    /**
     * Process output to determine if the specs/tests all passed (return TRUE)
     * or encountered a failure/error (return FALSE)
     *
     * @param string $output Output from the test framework being executed
     * @return bool
     */
    public function processOutput($output)
    {
        $lines = explode("\n", $output);
        $useful = array_slice($lines, 2);
        foreach ($useful as $line) {
            if ($line == "\n") {
                break;
            }
            if (preg_match("/.*[EF].*/", $line)) {
                return false;
            }
        }
        return true;
    }

}
