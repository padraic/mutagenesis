<?php

/** Mutateme_Framework */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Framework.php';

class Mutateme_Console_Command
{

    /**
     *
     * @todo should not directly echo reporter since some will pass mesgs only
     * @param Mutateme_Console_Getopt $options
     */
    public static function main(Mutateme_Console_Getopt $options = null)
    {
        if (is_null($options)) {
            $options = new Mutateme_Console_Getopt;
        }

        $runner = new Mutateme_Runner;

        if (isset($options->workingdir)) {
            $runner->setWorkingDirectory($options->workingdir);
        } else {
            $tmp = sys_get_temp_dir();
            $workingTmp = $tmp . DIRECTORY_SEPARATOR . 'mutagens';
            $runner->setWorkingDirectory($workingTmp);
        }

        if (isset($options->basedir)) {
            $runner->setBaseDirectory($options->basedir);
        } else {
            $runner->setBaseDirectory(getcwd());
        }

        if (isset($options->sourcedir)) {
            $runner->setSourceDirectory($options->sourcedir);
        } elseif (file_exists($runner->getBaseDirectory() . DIRECTORY_SEPARATOR . 'src')) {
            $runner->setSourceDirectory($runner->getWorkingDirectory() . DIRECTORY_SEPARATOR . 'src');
        }  elseif (file_exists($runner->getBaseDirectory() . DIRECTORY_SEPARATOR . 'lib')) {
            $runner->setSourceDirectory($runner->getWorkingDirectory() . DIRECTORY_SEPARATOR . 'lib');
        } else {
        }

        if (isset($options->specdir)) {
            $runner->setSpecDirectory($options->specdir);
        } elseif (isset($options->testdir)) {
            $runner->setSpecDirectory($options->testdir);
        } elseif (file_exists($runner->getBaseDirectory() . DIRECTORY_SEPARATOR . 'specs')) {
            $runner->setSpecDirectory($runner->getWorkingDirectory() . DIRECTORY_SEPARATOR . 'specs');
        } elseif (file_exists($runner->getBaseDirectory() . DIRECTORY_SEPARATOR . 'tests')) {
            $runner->setSpecDirectory($runner->getWorkingDirectory() . DIRECTORY_SEPARATOR . 'tests');
        }

        if (isset($options->command)) {
            $runner->setAdapterName($options->command);
        } else {
            $runner->setAdapterName('phpunit');
        }

        $result = $runner->execute();
        echo $result;
    }

}

Mutateme_Console_Command::main();
