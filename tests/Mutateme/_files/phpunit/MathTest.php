<?php

require_once dirname(__FILE__).'/Math.php';

class MathTest extends PHPUnit_Framework_TestCase
{
    public function testAdds()
    {
        $math = new PhpunitMath;
        $this->assertEquals(4, $math->add(2,2));
    }
}
