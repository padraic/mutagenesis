<?php

require_once 'PHPUnit/TextUI/Command.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

class Mutateme_Adapter_Phpunit_Runner extends PHPUnit_TextUI_Command
{

    public static function main(array $arguments)
    {
        $runner    = new PHPUnit_TextUI_TestRunner;

        $suite = $runner->getTest(
            $arguments['test'],
            $arguments['testFile']
        );

        if ($suite->testAt(0) instanceof PHPUnit_Framework_Warning &&
            strpos($suite->testAt(0)->getMessage(), 'No tests found in class') !== FALSE) {
            require_once 'PHPUnit/Util/Skeleton/Test.php';

            $skeleton = new PHPUnit_Util_Skeleton_Test(
                $arguments['test'],
                $arguments['testFile']
            );

            $result = $skeleton->generate(TRUE);

            if (!$result['incomplete']) {
                eval(str_replace(array('<?php', '?>'), '', $result['code']));
                $suite = new PHPUnit_Framework_TestSuite($arguments['test'] . 'Test');
            }
        }

        try {
            $runner->doRun(
              $suite,
              $arguments
            );
        } catch (Exception $e) {
            throw new RuntimeException(
              'Could not create and run test suite: ' . $e->getMessage()
            );
        }

    }

}
