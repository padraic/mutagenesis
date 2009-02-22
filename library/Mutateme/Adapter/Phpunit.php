<?php

require_once 'Mutateme/Adapter.php';

class Mutateme_Adapter_Phpunit extends Mutateme_Adapter
{

    public function execute(array $options = null)
    {
        $arguments = array();
        if (isset($options['test'])) {
            $arguments['test'] = $options['test'];
        } else {
            $arguments['test'] = 'AllTests';
        }
        if (isset($options['testFile'])) {
            $arguments['testFile'] = $options['testFile'];
        } else {
            $arguments['testFile'] = $options['specdir'].'/AllTests.php';
        }
        ob_start();
        ob_implicit_flush(false);
        if (!defined('PHPUnit_MAIN_METHOD')) {
            define('PHPUnit_MAIN_METHOD', 'undefined');
        }
        require_once 'Mutateme/Adapter/Phpunit/Runner.php';
        Mutateme_Adapter_Phpunit_Runner::main($arguments);
        $this->setOutput(ob_get_contents());
        ob_end_clean();
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
