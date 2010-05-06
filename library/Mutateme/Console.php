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

namespace Mutateme;

class Console
{

    protected static $_options = array();

    public static function main(array $options = null, \Mutateme\Runner $runner = null)
    {
        if (is_null($options)) {
            self::$_options = getopt(
                '',
                array(
                    'base::',
                    'src::',
                    'test::',
                    'adapter::'
                )
            );
        } else {
            self::$_options = $options;
        }

        if (is_null($runner)) {
            $runner = new \Mutateme\Runner;
        }

        self::setBaseDirectory($runner);
        self::setSourceDirectory($runner);
        self::setTestDirectory($runner);
        self::setAdapterName($runner);
    }

    protected static function setBaseDirectory(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['base'])) {
            $runner->setBaseDirectory(self::$_options['base']);
        } else {
            $runner->setBaseDirectory(getcwd());
        }
    }

    protected static function setSourceDirectory(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['src'])) {
            $runner->setSourceDirectory(self::$_options['src']);
        } else {
            $runner->setSourceDirectory(getcwd());
        }
    }

    protected static function setTestDirectory(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['test'])) {
            $runner->setTestDirectory(self::$_options['test']);
        } else {
            $runner->setTestDirectory(getcwd());
        }
    }

    protected static function setAdapterName(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['adapter'])) {
            $runner->setAdapterName(self::$_options['adapter']);
        } else {
            $runner->setAdapterName(getcwd());
        }
    }
    
}
