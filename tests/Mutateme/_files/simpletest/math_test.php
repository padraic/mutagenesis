<?php

require_once('simpletest/autorun.php');
require_once dirname(__FILE__) . '/Math.php';

class TestOfMath extends UnitTestCase {

    public function testAdd()
    {
        $math = new SimpletestMath;
        $this->assertEqual(4, $math->add(2,2));
    }
}
