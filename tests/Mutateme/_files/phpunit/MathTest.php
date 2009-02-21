<?php

require_once dirname(__FILE__).'/Math.php';

class MathTest extends PHPUnit_Framework_TestCase
{
    public function testAdds()
    {
        $math = new PhpunitMath;
        $this->assertEquals(1, $math->add(0,1));
    }
}
