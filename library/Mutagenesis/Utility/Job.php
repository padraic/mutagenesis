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

namespace Mutagenesis\Utility;

class Job
{
    
    /**
     * Generate a new Job script to be executed under a separate PHP process
     *
     * @param array $mutation Mutation data and objects to be used
     * @return string
     */
    public function generate(array $mutation = array(), array $args = array(), $bootstrap = null)
    {
        $serializedArgs = addslashes(serialize($args));
        $serializedMutation = addslashes(serialize($mutation));
        if (is_null($bootstrap)) {
            $bootstrap = 'null';
        } else {
            addslashes($bootstrap);
        }
        $script = <<<SCRIPT
<?php
require_once 'Mutagenesis/Loader.php';
\$loader = new \Mutagenesis\Loader;
\$loader->register();
\Mutagenesis\Adapter\PHPUnit::main(
    "{$serializedArgs}",
    "{$serializedMutation}",
    "{$bootstrap}"
);
SCRIPT;
        return $script;
    }
    
}
