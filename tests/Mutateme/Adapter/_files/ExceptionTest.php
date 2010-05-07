<?php

class ExceptionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @group disabled
     */
    public function testSomeException()
    {
        throw new Exception('exception');
    }  

}
