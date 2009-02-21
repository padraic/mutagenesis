<?php

class Mutateme_Runkit
{

    protected $_methodPreserveCode = '';

    public function applyMutation(array $mutation)
    {
        require_once $mutation['mutation']->getFilename();
        $newBlock = $mutation['mutation']->mutate($mutation['tokens'], $mutation['index']);
        $this->_methodPreserveCode = md5($mutation['method']);
        runkit_method_rename($mutation['class'], $mutation['method'], $mutation['method'].$this->_methodPreserveCode);
        runkit_method_add($mutation['class'], $mutation['method'], $mutation['args'], $newBlock);
    }

    public function reverseMutation(array $mutation)
    {
        runkit_method_remove($mutation['class'], $mutation['method']);
        runkit_method_rename($mutation['class'], $mutation['method'].$this->_methodPreserveCode, $mutation['method']);
    }
}
