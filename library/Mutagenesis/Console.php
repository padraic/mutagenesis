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
     * @param \Mutagenesis\Runner\RunnerAbstract $runner Optional custom runner
     */
    public static function main(array $options = null,
    \Mutagenesis\Runner\RunnerAbstract $runner = null)
    {
        if (is_null($options)) {
            self::$_options = getopt(
                '',
                array(
                    'base::',
                    'src::',
                    'tests::',
                    'adapter::',
                    'bootstrap::',
                    'options::',
                    'timeout::',
                    'detail-captures::'
                )
            );
        } else {
            self::$_options = $options;
        }

        if (is_null($runner)) {
            $runner = new \Mutagenesis\Runner\Base;
        }

        self::setBaseDirectory($runner);
        self::setSourceDirectory($runner);
        self::setTestDirectory($runner);
        self::setAdapterName($runner);
        self::setBootstrap($runner);
        self::setAdapterOptions($runner);
        self::setTimeout($runner);
        self::setDetailCaptures($runner);

        $result = $runner->execute();
        echo $result;
    }

    /**
     * Set a base directory for the provided runner
     *
     * @param \Mutagenesis\Runner\RunnerAbstract $runner
     */
    protected static function setBaseDirectory(\Mutagenesis\Runner\RunnerAbstract $runner)
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
     * @param \Mutagenesis\Runner\RunnerAbstract $runner
     */
    protected static function setSourceDirectory(\Mutagenesis\Runner\RunnerAbstract $runner)
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
     * @param \Mutagenesis\Runner\RunnerAbstract $runner
     */
    protected static function setTestDirectory(\Mutagenesis\Runner\RunnerAbstract $runner)
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
     * @param \Mutagenesis\Runner\RunnerAbstract $runner
     */
    protected static function setAdapterName(\Mutagenesis\Runner\RunnerAbstract $runner)
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
     * @param \Mutagenesis\Runner\RunnerAbstract $runner
     */
    protected static function setAdapterOptions(\Mutagenesis\Runner\RunnerAbstract $runner)
    {
        if (isset(self::$_options['options'])) {
            $runner->setAdapterOption(self::$_options['options']);
        }
    }
    
    /**
     * Set timeout in seconds to apply to each test run. The default timeout
     * is 120 seconds.
     *
     * @param \Mutagenesis\Runner\RunnerAbstract $runner
     */
    protected static function setTimeout(\Mutagenesis\Runner\RunnerAbstract $runner)
    {
        if (isset(self::$_options['timeout'])) {
            $runner->setTimeout(self::$_options['timeout']);
        }
    }
    
    /**
     * Set the path to a bootstrap file used when testing. This allows
     * for registering autoloaders and such, for example TestHelper.php or
     * Bootstrap.php are common for PHPUnit.
     *
     * @param \Mutagenesis\Runner\RunnerAbstract $runner
     */
    protected static function setBootstrap(\Mutagenesis\Runner\RunnerAbstract $runner)
    {
        if (isset(self::$_options['bootstrap'])) {
            $runner->setBootstrap(self::$_options['bootstrap']);
        }
    }
    
    /**
     * Set timeout in seconds to apply to each test run. The default timeout
     * is 120 seconds.
     *
     * @param \Mutagenesis\Runner\RunnerAbstract $runner
     */
    protected static function setDetailCaptures(\Mutagenesis\Runner\RunnerAbstract $runner)
    {
        if (isset(self::$_options['detail-captures'])) {
            $runner->setDetailCaptures(true);
        }
    }
    
}
