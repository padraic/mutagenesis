<?php

abstract class Mutateme_Adapter
{

    protected $_output = '';

    abstract public function execute(array $options = null);

    public function setOutput($output)
    {
        $this->_output = $output;
    }

    public function getOutput()
    {
        return $this->_output;
    }

}
