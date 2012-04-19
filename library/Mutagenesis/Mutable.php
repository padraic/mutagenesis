<?php
/**
 * Mutagenesis
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mutateme/blob/rewrite/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mutagenesis
 * @package    Mutagenesis
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/blob/rewrite/LICENSE New BSD License
 */

namespace Mutagenesis;

class Mutable
{

    /**
     * Name and relative path of the file to be mutated
     *
     * @var string
     */
    protected $_filename = null;

    /**
     *  An array of generated mutations to be sequentially tested
     *
     * @var array
     */
    protected $_mutations = array();

    /**
     *  Array of mutable elements located in file
     *
     * @var array
     */
    protected $_mutables = array();

    /**
     * Constructor; sets name and relative path of the file being mutated
     *
     * @param string $filename
     */
    public function __construct($filename = null)
    {
        $this->setFilename($filename);
    }

    /**
     * Based on the current file, generate mutations
     *
     * @return void
     */
    public function generate()
    {
        $this->_mutables = $this->_parseMutables();
        $this->_parseTokensToMutations($this->_mutables);
        return $this;
    }

    /**
     * Cleanup routines for memory management
     */
    public function cleanup()
    {
        unset($this->_mutations, $this->_mutables);
    }

    /**
     * Set the file path of the file which is currently being assessed for
     * mutations.
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        // checks
        $this->_filename = $filename;
    }

    /**
     * Return the file path of the file which is currently being assessed for
     * mutations.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
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
        $typeClass = '\\Mutagenesis\\Mutation\\' . $type;
        // I know, wtf?!
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
            $mutationClass =  'Mutagenesis\\Mutation\\' . $type;
            if (!class_exists($mutationClass)) {
                require_once str_replace('\\', '/', ltrim($mutationClass, '\\')) . '.php';
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
            case T_DEC:
                $type = 'OperatorDecrement';
                break;
            case T_BOOLEAN_AND:
                $type = 'BooleanAnd';
                break;
            case T_BOOLEAN_OR:
                $type = 'BooleanOr';
                break;
            case T_STRING:
                $type = $this->_parseTString($token);
                break;
        }
        if (!empty($type)) {
            $mutationClass =  'Mutagenesis\\Mutation\\' . $type;
            if (!class_exists($mutationClass)) {
                // todo: given we're autoloading, could we not just kick up an exception here?
                require_once str_replace('\\', '/', ltrim($mutationClass, '\\')) . '.php';
            }
            $mutation = new $mutationClass($this->getFilename());
            return $mutation;
        }
        return null;
    }

    /**
     * Parse a T_STRING value to identify a possible mutation type
     *
     * @param array $token
     * @return string
     */
    public function _parseTString(array $token)
    {
        $type = null;
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
        $static = false;
        $staticClassCapture = true;
        $namespace = "";
        foreach ($tokens as $index=>$token) {
            if(is_array($token) && $token[0] == T_NAMESPACE) {
                $namespace = "\\" . $tokens[$index+2][1];
                $i = 3;
                while(is_array($tokens[$index+$i]) && $token[0] = T_NS_SEPARATOR) {
                    $namespace.= "\\" . $tokens[$index+$i+1][1];
                    $i+=2;
                }
            }
            if(is_array($token) && $token[0] == T_STATIC && $staticClassCapture === true) {
                $static = true;
                $staticClassCapture = false;
                continue;
            }
            // get class name
            if (is_array($token) && ($token[0] == T_CLASS || $token[0] == T_INTERFACE)) {
                $className = $tokens[$index+2][1];
                $staticClassCapture = false;
                continue;
            }
            // get method name
            if (is_array($token) && $token[0] == T_FUNCTION && !$inblock) {

                if (!is_array($tokens[$index+2]) || $tokens[$index+2][0] != T_STRING || $tokens[$index+1] == "(") {
                    // probably a closure, skip for now
                    continue;
                }
                $methodName = $tokens[$index+2][1];
                $inarg = true;
                $mutable = array(
                    'file' => $this->getFilename(),
                    'class' => $namespace . "\\" . $className,
                    'method' => $methodName
                );
                continue;
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
                } elseif ($roundcount == 0) {
                    $mutable['args'] = $this->_reconstructFromTokens($argTokens);
                    $argTokens = array();
                    $inarg = false;
                    $inblock = true;
                }
                continue;
            }
            // Get the method's block code
            if ($inblock) {
                if ($token == '{' || (is_array($token) && $token[0] == T_CURLY_OPEN)) {
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
                    $staticClassCapture = true;
                }
            }
        }
        return $methods;
    }

    /**
     * Reconstruct a string of source code from its constituent tokens
     *
     * @param array $tokens
     * @return string
     */
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
