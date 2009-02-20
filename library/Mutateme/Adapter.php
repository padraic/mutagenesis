<?php

abstract class Mutateme_Adapter
{

    protected $_runner = null;

    protected $_output = '';

    protected $_command = '';

    public function __construct(Mutateme_Runner $runner = null)
    {
        $this->_runner = $runner;
    }

    abstract public function execute();

    public function getRunner()
    {
        return $this->_runner;
    }

    public function setOutput($shellOutput)
    {
        $this->_output = $shellOutput;
    }

    public function getOutput()
    {
        return $this->_output;
    }

    public function setCommand($command)
    {
        if (!preg_match("/^phpunit|phpspec/", $command)) {
            throw new Mutateme_Exception('Unrecognised command type: Must utilise a recognised command commencing with one of "phpunit" or "phpspec"');
        }
        $this->_command = $command;
    }

}
