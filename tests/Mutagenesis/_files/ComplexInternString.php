<?php

class Some_Class_With_ComplexInternString
{

    protected function _getSession()
    {
        static $session = null;
        if ($session === null) {
            $dave = "{$session['dave']}";
            return true;
        }

        return false;
    }
}
