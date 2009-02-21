<?php

class Mutateme_MutableFile
{

    protected $_fileName = null;

    protected $_mutations = array();

    protected $_mutables = array();

    /**
     * Constructor; requires the file path for the PHP file to mutate
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->_fileName = $file;
    }

    /**
     * Generates an array of all possible supported mutations that
     * can be utilised for the given file.
     *
     * @return void
     */
    public function generateMutations()
    {
        $this->_mutables = $this->_parseMutables();
        $this->_parseTokensToMutations($this->_mutables);
    }

    /**
     * Return the file path of the file which is currently being assessed for
     * mutations.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_fileName;
    }

    /**
     * Get an array of Class & Method indexed mutations containing the mutated
     * token and that token's index in the method's block code.
     *
     * @return array
     */
    public function getMutations()
    {
        return $this->_mutations;
    }

    /**
     * Get an array of method metainfo in tokenised form representing methods
     * which are capable of being mutated. Note: This does not guarantee they
     * will be mutated since this depends on the scope of supported mutations.
     *
     * @return array
     */
    public function getMutables()
    {
        return $this->_mutables;
    }

    /**
     * Check whether the current file will contain a mutation of the given type
     *
     * @param string $type The mutation type as documented
     * @return bool
     */
    public function hasMutation($type)
    {
        $typeClass = 'Mutateme_Mutation_' . $type;
        $mutations = array_values(array_values(array_values($this->getMutations())));
        foreach ($mutations as $mutation) {
            if ($mutation instanceof $typeClass) {
                return true;
            }
        }
        return false;
    }

    /**
     * Based on the internal array of mutable methods, generate another
     * internal array of supported mutations accessible using getMutations().
     *
     * @param array $mutables
     * @return void
     */
    protected function _parseTokensToMutations(array $mutables)
    {
        foreach ($mutables as $method) {
            if (!isset($method['tokens']) || empty($method['tokens'])) {
                continue;
            }
            foreach ($method['tokens'] as $index=>$token) {
                if (is_string($token)) {
                    $mutation = $this->_parseStringToken($token, $index);
                } else {
                    $mutation = $this->_parseToken($token, $index);
                }
                if (!is_null($mutation)) {
                    $this->_mutations[] = $method + array(
                        'index' => $index,
                        'mutation' => $mutation
                    );
                }
            }
        }
    }

    /**
     * Parse a given token (in string form) to identify its type and ascertain
     * whether it can be replaced with a mutated form. The mutated form, if
     * any, is returned for future integration into a mutated version of the
     * source code being tested.
     *
     * @param array $token The token to check for viable mutations
     * @param integer $index The index of the token in the method's body
     * @return mixed Return null if no mutation, or a mutation object
     */
    protected function _parseStringToken($token, $index)
    {
        $type = '';
        switch ($token) {
            case '+':
                $type = 'OperatorAddition';
                break;
            case '-':
                $type = 'OperatorSubtraction';
                break;
        }
        if (!empty($type)) {
            $mutationClass =  'Mutateme_Mutation_' . $type;
            if (!class_exists($mutationClass)) {
                require_once str_replace('_', '/', $mutationClass) . '.php';
            }
            $mutation = new $mutationClass($this->getFilename());
            return $mutation;
        }
    }

    /**
     * Parse a given token (in array form) to identify its type and ascertain
     * whether it can be replaced with a mutated form. The mutated form, if
     * any, is returned for future integration into a mutated version of the
     * source code being tested.
     *
     * @param array $token The token to check for viable mutations
     * @param integer $index The index of the token in the method's body
     * @return mixed Return null if no mutation, or a mutation object
     */
    protected function _parseToken(array $token, $index)
    {
        $type = '';
        switch ($token[0]) {
            case T_INC:
                $type = 'OperatorIncrement';
                break;
            case T_STRING:
                $type = $this->_parseTString($token);
                break;
        }
        if (!empty($type)) {
            $mutationClass =  'Mutateme_Mutation_' . $type;
            if (!class_exists($mutationClass)) {
                require_once str_replace('_', '/', $mutationClass) . '.php';
            }
            $mutation = new $mutationClass($this->getFilename());
            return $mutation;
        }
    }

    public function _parseTString(array $token)
    {
        if (strtolower($token[1]) == 'true') {
            $type = 'BooleanTrue';
        } elseif (strtolower($token[1]) == 'false') {
            $type = 'BooleanFalse';
        }
        return $type;
    }

    /**
     * Parse the given file into an array of method metainformation including
     * class name, method name, file name, method arguments, and method body
     * tokens.
     *
     * @return array
     */
    protected function _parseMutables()
    {
        $tokens = token_get_all(
            file_get_contents($this->getFilename())
        );
        $inblock = false;
        $inarg = false;
        $curlycount = 0;
        $roundcount = 0;
        $blockTokens = array();
        $argTokens = array();
        $methods = array();
        $mutable = array();
        foreach ($tokens as $index=>$token) {
            // get class name
            if (is_array($token) && $token[0] == T_CLASS) {
                $className = $tokens[$index+2][1];
            }
            // get method name
            if (is_array($token) && $token[0] == T_FUNCTION) {
                $methodName = $tokens[$index+2][1];
                // notify loop we are in a new method
                $inblock = true;
                $inarg = true;
                $mutable = array(
                    'file' => $this->getFilename(),
                    'class' => $className,
                    'method' => $methodName
                );
            }
            // Get the method's parameter string
            if ($inarg) {
                if ($token == '(') {
                    $roundcount += 1;
                } elseif ($token == ')') {
                    $roundcount -= 1;
                }
                if ($roundcount == 1 && $token == '(') {
                    continue;
                } elseif ($roundcount >= 1) {
                    $argTokens[] = $token;
                } elseif ($roundcount == 0 && count($argTokens) > 0) {
                    $mutable['args'] = $this->_reconstructFromTokens($argTokens);
                    $argTokens = array();
                    $inarg = false;
                }
            }
            // Get the method's block code
            if ($inblock) {
                if ($token == '{') {
                    $curlycount += 1;
                } elseif ($token == '}') {
                    $curlycount -= 1;
                }
                if ($curlycount == 1 && $token == '{') {
                    continue;
                } elseif ($curlycount >= 1) {
                    if (is_array($token) && $token[0] == 370) {
                        continue;
                    }
                    $blockTokens[] = $token;
                } elseif ($curlycount == 0 && count($blockTokens) > 0) {
                    $mutable['tokens'] = $blockTokens;
                    $methods[] = $mutable;
                    $mutable = array();
                    $blockTokens = array();
                    $inblock = false;
                }
            }
        }
        return $methods;
    }

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
