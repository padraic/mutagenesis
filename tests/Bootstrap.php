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

/*
 * Set error reporting to the level to which Mutagenesis code must comply.
 */
error_reporting(E_ALL | E_STRICT);

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$root    = realpath(dirname(dirname(__FILE__)));
$library = "$root/library";
$tests   = "$root/tests";

/*
 * Prepend the Mutagenesis library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the code and tests that would supercede
 * this copy.
 */
$path = array(
    $library,
    $tests,
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === true &&
    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {

    /*
     * Add Mutagenesis library/ directory to the PHPUnit code coverage
     * whitelist. This has the effect that only production code source files
     * appear in the code coverage report and that all production code source
     * files, even those that are not covered by a test yet, are processed.
     */
    PHPUnit_Util_Filter::addDirectoryToWhitelist($library);

    /*
     * Omit from code coverage reports the contents of the tests directory
     */
    foreach (array('.php', '.phtml', '.csv', '.inc') as $suffix) {
        PHPUnit_Util_Filter::addDirectoryToFilter($tests, $suffix);
    }
    PHPUnit_Util_Filter::addDirectoryToFilter(PEAR_INSTALL_DIR);
    PHPUnit_Util_Filter::addDirectoryToFilter(PHP_LIBDIR);
}

/**
 * Setup autoloaders!
 */

if (file_exists(__DIR__.'/../vendor/.composer/autoload.php')) {
    include __DIR__.'/../vendor/.composer/autoload.php';
} else if (file_exists(__DIR__.'/../../../.composer/autoload.php')) {
    include __DIR__.'/../../../.composer/autoload.php';
} else {

    /**
     * Check unit test deps are in place (this also requires Mutagenesis to be available from
     * the php.ini include path for all processes opened during testing)
     */
    if (stream_resolve_include_path('Mutagenesis/Loader.php') === false) {
        throw new Exception(
            'Please install Mutagenesis prior to running the unit tests. Since '
            . 'Mutagenesis operates across multiple PHP processes under testing, '
            . 'it must be installed or accessible from your php.ini defined include_path '
            . 'so that any PHP process can easily locate it.'
        );
    }
    if (stream_resolve_include_path('Mockery/Loader.php') === false) {
        throw new Exception(
            'Mutagenesis unit tests rely on the Mockery test double framework. See '
            . 'https://github.com/padraic/mockery for instructions on how to install it '
            . 'using PEAR or Composer.'
        );
    }


    require_once 'Mutagenesis/Loader.php';
    $loader = new \Mutagenesis\Loader;
    $loader->register();

    require_once 'Mockery/Loader.php';
    $loader = new \Mockery\Loader;
    $loader->register(true);

}

/*
 * Unset global variables that are no longer needed.
 */
unset($root, $library, $tests, $path);

