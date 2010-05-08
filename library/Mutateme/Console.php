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

    /**
     * Options passed across the command line parsed by getopt()
     *
     * @var array
     */
    protected static $_options = array();

    /**
     * Sets up options, and initialises the Runner to perform mutation
     * tests and echo out the results
     *
     * @param array $options
     * @param \Mutateme\Runner $runner Optional custom runner
     */
    public static function main(array $options = null, \Mutateme\Runner $runner = null)
    {
        if (is_null($options)) {
            self::$_options = getopt(
                '',
                array(
                    'base::',
                    'src::',
                    'tests::',
                    'adapter::',
                    'options::'
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
        self::setAdapterOptions($runner);

        $result = $runner->execute();
        echo $result;
    }

    /**
     * Set a base directory for the provided runner
     *
     * @param \Mutateme\Runner $runner
     */
    protected static function setBaseDirectory(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['base'])) {
            $runner->setBaseDirectory(self::$_options['base']);
        } else {
            $runner->setBaseDirectory(getcwd());
        }
    }

    /**
     * Set a source directory for the provided runner
     *
     * @param \Mutateme\Runner $runner
     */
    protected static function setSourceDirectory(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['src'])) {
            $runner->setSourceDirectory(self::$_options['src']);
        } else {
            $runner->setSourceDirectory(getcwd());
        }
    }

    /**
     * Set a tests directory for the provided runner
     *
     * @param \Mutateme\Runner $runner
     */
    protected static function setTestDirectory(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['tests'])) {
            $runner->setTestDirectory(self::$_options['tests']);
        } else {
            $runner->setTestDirectory(getcwd());
        }
    }

    /**
     * Set an adapter name to use for the provided runner. If none is
     * provided, the PHPUnit adapter name is set by default.
     *
     * @param \Mutateme\Runner $runner
     */
    protected static function setAdapterName(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['adapter'])) {
            $runner->setAdapterName(self::$_options['adapter']);
        } else {
            $runner->setAdapterName('phpunit');
        }
    }

    /**
     * Set options to be parsed and passed to the adapter instance used by
     * the runner
     *
     * @param \Mutateme\Runner $runner
     */
    protected static function setAdapterOptions(\Mutateme\Runner $runner)
    {
        if (isset(self::$_options['options'])) {
            $runner->setAdapterOptions(self::$_options['options']);
        }
    }
    
}
