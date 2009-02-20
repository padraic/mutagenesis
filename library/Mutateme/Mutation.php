<?php

@require_once 'Text/Diff.php';

@require_once 'Text/Diff/Renderer/unified.php';

abstract class Mutateme_Mutation
{

    protected $_tokensOriginal = null;

    protected $_tokensMutated = null;

    protected $_fileName;

    public function __construct($filename)
    {
        $this->_fileName = $filename;
    }

    public function mutate($mutable, $index) {
        $this->_tokensOriginal = $mutable;
        $this->_tokensMutated = $this->applyMutation($this->_tokensOriginal, $index);
        return $this->_reconstructFromTokens($this->_tokensMutated);
    }

    public function getFilename()
    {
        return $this->_fileName;
    }

    public function getDiff()
    {
        $original = $this->_reconstructFromTokens($this->_tokensOriginal);
        $mutated = $this->_reconstructFromTokens($this->_tokensMutated);
        $diff = new Text_Diff(
            'auto',
            array(explode("\n", $original), explode("\n", $mutated))
        );
        $diffRenderer = new Text_Diff_Renderer_unified;
        $difference = $diffRenderer->render($diff);
        $difference = 'Index: ' . $this->_fileName . PHP_EOL
            . str_repeat('=', 67) . PHP_EOL . $difference;
        return $difference;
    }

    abstract public function getMutation(array $tokens, $index);

    protected function _reconstructFromTokens(array $tokens)
    {
        $str = '';
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $str .= $token;
            } else {
                $str .= $token[1];
            }
        }
        return $str;
    }

}
