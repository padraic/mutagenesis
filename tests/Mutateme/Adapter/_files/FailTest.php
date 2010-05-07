<?php

class FailTest extends PHPUnit_Framework_TestCase
{

    /**
     * @group disabled
     */
    public function testSomeFail()
    {
        $this->assertTrue(false);
    }  

}
