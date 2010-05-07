<?php

class ErrorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @group disabled
     */
    public function testSomeError()
    {
        trigger_error('error', E_USER_NOTICE);
    }  

}
