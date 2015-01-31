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
    public function generate(array $mutation = array(), array $args = array(), $timeout = 60, $bootstrap = null)
    {
        $serializedArgs = addslashes(serialize($args));
        $serializedMutation = addcslashes(serialize($mutation), "'\\");
        if (is_null($bootstrap)) {
            $bootstrap = 'null';
        } else {
            $bootstrap = '"' . addslashes($bootstrap) . '"';
        }

        if (file_exists(__DIR__.'/../../../vendor/.composer/autoload.php')) {
            $autoload =  'include "'.realpath(__DIR__.'/../../../vendor/.composer/autoload.php').'";';
        } else if (file_exists(__DIR__.'/../../../../../.composer/autoload.php')) {
            $autoload = 'include "'.realpath(__DIR__.'/../../../../../.composer/autoload.php').'";';
        } else { 
            $mutagenesisPath = realpath(__DIR__ . '/../../');
            $autoload = <<<EOS
require_once 'Mutagenesis/Loader.php';
\$loader = new \Mutagenesis\Loader;
\$loader->register();
EOS;
        }

        $script = <<<SCRIPT
<?php

namespace MutagenesisEnv;

declare(ticks = 1);
require_once 'PHPUnit/Autoload.php';
$autoload
class Job {
    static function main () {
        \Mutagenesis\Adapter\Phpunit::main(
            "{$serializedArgs}",
            '{$serializedMutation}',
            {$bootstrap}
        );
    }
    static function timeout() {
        throw new \\Exception('Timed Out');
    }
}
pcntl_signal(SIGALRM, array('\\MutagenesisEnv\\Job', 'timeout'), TRUE);
pcntl_alarm({$timeout});
try {
    Job::main();
} catch (\\Exception \$e) {
    pcntl_alarm(0);
    throw \$e;
}
pcntl_alarm(0);
SCRIPT;
        return $script;
    }
    
}
