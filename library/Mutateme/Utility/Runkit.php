<?php
/**
 * Mutateme
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
 * @category   Mutateme
 * @package    Mutateme
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mutateme/blob/rewrite/LICENSE New BSD License
 */

namespace Mutateme\Utility;

class Runkit
{

    /**
     * Method signature hash appended to a replaced method's name so it can
     * be reinstated later without any need to separately store entire method
     * related code blocks.
     *
     * @var string
     */
    protected $_methodPreserveCode = '';

    /**
     * Apply a mutation to the relevant file
     *
     * @param array $mutation
     */
    public function applyMutation(array $mutation)
    {
        require_once $mutation['mutation']->getFilename();
        $newBlock = $mutation['mutation']
            ->mutate($mutation['tokens'], $mutation['index']);
        $this->_methodPreserveCode = md5($mutation['method']);
        if (runkit_method_rename(
            $mutation['class'],
            $mutation['method'],
            $mutation['method'] . $this->_methodPreserveCode
        ) == false) {
            throw new \Exception(
                'runkit_method_rename() failed from ' . $mutation['class']
                . '::' . $mutation['method'] . ' to ' . $mutation['class']
                . '::' . $mutation['method'] . $this->_methodPreserveCode
                . ' (mutation application)'
            );
        }
        /**
         * TODO: Why are no args set in some cases? Bug?
         */
        $margs = (isset($mutation['args']) ? $mutation['args'] : '');
        if(runkit_method_add(
            $mutation['class'],
            $mutation['method'],
            $margs,
            $newBlock,
            $this->getMethodFlags($mutation)
        ) == false) {
            throw new \Exception(
                'runkit_method_add() failed when adding original '
                . $mutation['class'] . '::' . $method['method']
                . '(' . var_export($mutation['args']) . ') with ' . $newBlock
            );
        }
    }

    /**
     * Reverse a previously applied mutation to the given file
     *
     * @param array $mutation
     */
    public function reverseMutation(array $mutation)
    {
        if(runkit_method_remove(
            $mutation['class'],
            $mutation['method']
        ) == false) {
            throw new \Exception(
                'runkit_method_remove() failed attempting to remove '
                . $mutation['class'] . '::' . $mutation['method']
            );
        }
        if(runkit_method_rename(
            $mutation['class'],
            $mutation['method'] . $this->_methodPreserveCode,
            $mutation['method']
        ) == false) {
            throw new \Exception(
                'runkit_method_rename() failed renaming from '
                . $mutation['class'] . '::' . $mutation['method']
                . $this->_methodPreserveCode . ' to ' . $mutation['class']
                . '::' . $mutation['method'] . ' (mutation reversal)'
            );
        }
    }

    /**
     * Get the appropriate ext/runkit method flag value to use during
     * a replacement via the runkit methods
     *
     * @param array $mutation
     * @return int
     */
    public function getMethodFlags(array $mutation)
    {
        $reflectionClass = new \ReflectionClass($mutation['class']);
        $reflectionMethod = $reflectionClass->getMethod(
            $mutation['method'] . $this->_methodPreserveCode
        );
        $static = null;
        $access = null;
        if ($reflectionMethod->isPublic()) {
            $access = RUNKIT_ACC_PUBLIC;
        } elseif ($reflectionMethod->isProtected()) {
            $access = RUNKIT_ACC_PROTECTED;
        } elseif ($reflectionMethod->isPrivate()) {
            $access = RUNKIT_ACC_PRIVATE;
        }
        if (defined('RUNKIT_ACC_STATIC') && $reflectionMethod->isStatic()) {
            $static = RUNKIT_ACC_STATIC;
        }
        if (!is_null($static)) {
            return $access | $static;
        }
        return $access;
    }
    
}
