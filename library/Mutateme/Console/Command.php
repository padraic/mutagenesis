<?php

/** Mutateme_Framework */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Framework.php';

class Mutateme_Console_Command
{

    /**
     *
     * @param Mutateme_Console_Getopt $options
     */
    public static function main(Mutateme_Console_Getopt $options = null)
    {
        if (is_null($options)) {
            $options = new Mutateme_Console_Getopt;
        }

        $runner = new Mutateme_Runner;

        if (isset($options->basedir)) {
            $runner->setBaseDirectory($options->basedir);
        } else {
            $runner->setBaseDirectory(getcwd());
        }

        if (isset($options->srcdir)) {
            if (!file_exists($options->srcdir)) {
                throw new Exception('Provided source code directory via --srcdir does not exist');
            } else {
                $runner->setSourceDirectory($options->srcdir);
            }
        } elseif (file_exists($runner->getBaseDirectory() . '/src')) {
            $runner->setSourceDirectory($runner->getBaseDirectory() . '/src');
        }  elseif (file_exists($runner->getBaseDirectory() . '/lib')) {
            $runner->setSourceDirectory($runner->getBaseDirectory() . '/lib');
        }  elseif (file_exists($runner->getBaseDirectory() . '/library')) {
            $runner->setSourceDirectory($runner->getBaseDirectory() . '/library');
        } else {
            throw new Exception('Unable to determine the location of source code; please use the --srcdir command line option');
        }

        if (isset($options->specdir)) {
            if (!file_exists($options->specdir)) {
                throw new Exception('Provided tests/specs directory via --testdir or --specdir does not exist');
            } else {
                $runner->setSpecDirectory($options->specdir);
            }
        } elseif (isset($options->testdir)) {
            $runner->setSpecDirectory($options->testdir);
        } elseif (file_exists($runner->getBaseDirectory() . '/tests')) {
            $runner->setSpecDirectory($runner->getBaseDirectory() . '/tests');
        } elseif (file_exists($runner->getBaseDirectory() . '/specs')) {
            $runner->setSpecDirectory($runner->getBaseDirectory() . '/specs');
        } else {
            throw new Exception('Unable to determine the location of tests/specs; please use the --testdir or --specdir command line options');
        }

        // handle spec cases

        if (isset($options->adapter)) {
            $runner->setAdapterName($options->adapter);
        } else {
            $runner->setAdapterName('phpunit'); //try adding autodetection a bit later!
        }

        // phpunit/simpletest cases

        if (isset($options->test)) {
            $runner->setOption('test', $options->test);
        }
        if (isset($options->testfile)) {
            if (!file_exists($options->testfile)) {
                $runner->setOption('testfile', $runner->getSpecDirectory(). '/' .$options->testfile);
            } else {
                $runner->setOption('testfile', $options->testfile);
            }
        }

        $result = $runner->execute();
        echo $result;
    }

}

Mutateme_Console_Command::main();
