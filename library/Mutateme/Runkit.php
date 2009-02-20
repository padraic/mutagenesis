<?php

class Mutateme_Runkit
{

    public function applyMutation(Mutateme_Mutation $mutation)
    {
        require_once $mutation->getFilename();

    }
}
