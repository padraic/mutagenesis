<?php

class Mutateme_Runkit
{
    protected $_methodPreserveCode = '';

    public function applyMutation(array $mutation)
    {

        require_once $mutation['mutation']->getFilename();
        $newBlock = $mutation['mutation']->mutate($mutation['tokens'], $mutation['index']);
        $this->_methodPreserveCode = md5($mutation['method']);
        if(runkit_method_rename($mutation['class'], $mutation['method'], $mutation['method'].$this->_methodPreserveCode) == false) {
            throw new Exception("Runkit Rename failed: ".$mutation['class']."::".$mutation['method']." into ".$mutation['method'].$this->_methodPreserveCode);
        }
        if(runkit_method_add($mutation['class'], $mutation['method'], $mutation['args'], $newBlock) == false) {
            throw new Exception("Runkit Add failed: ".$mutation['class']."::".$mutation['method']."(".var_export($mutation['args']).") with ".$newBlock);
        }
    }

    public function reverseMutation(array $mutation)
    {
        if(runkit_method_remove($mutation['class'], $mutation['method']) == false) {
            throw new Exception("Runkit Remove failed: ".$mutation['class']."::".$mutation['method']);
        }
        if(runkit_method_rename($mutation['class'], $mutation['method'].$this->_methodPreserveCode, $mutation['method']) == false) {
            throw new Exception("Runkit Rename failed: ".$mutation['class']."::".$mutation['method'].$this->_methodPreserveCode." into ".$mutation['method']);
        }
    }
}
