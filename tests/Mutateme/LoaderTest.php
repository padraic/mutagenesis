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

class Mutagenesis_LoaderTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        spl_autoload_unregister('\Mutagenesis\Loader::loadClass');
    }

    public function testCallingRegisterRegistersSelfAsSplAutoloaderFunction()
    {
        require_once 'Mutagenesis/Loader.php';
        $loader = new \Mutagenesis\Loader;
        $loader->register();
        $expected = array($loader, 'loadClass');
        $this->assertTrue(in_array($expected, spl_autoload_functions()));
    }

    public function tearDown()
    {
        $loader = new \Mutagenesis\Loader;
        $loader->register();
    }

}
