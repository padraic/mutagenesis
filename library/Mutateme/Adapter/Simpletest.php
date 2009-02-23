<?php

require_once 'Mutateme/Adapter.php';

class Mutateme_Adapter_Simpletest extends Mutateme_Adapter
{

    public function execute(array $options = null)
    {
        if (isset($options['testFile'])) {
            $testFile = $options['testFile'];
        } else {
            $testFile = $options['specdir'].'/all_tests.php';
        }
        ob_start();
        ob_implicit_flush(false);
        include $testFile;
        $this->setOutput(ob_get_contents());
        ob_end_clean();
        if (preg_match("/^\<\!DOCTYPE/", $this->getOutput())) {
            return $this->processHtmlOutput($this->getOutput());
        }
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
        $useful = array_slice($lines, 0);
        foreach ($useful as $line) {
            if ($line == "\n") {
                break;
            }
            if (preg_match("/^OK/", $line)) {
                return true;
            }
        }
        return false;
    }

}
