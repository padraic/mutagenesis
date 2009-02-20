<?php

require_once 'Mutateme/Mutation.php';

class Mutateme_Mutation_BooleanFalse extends Mutateme_Mutation
{

    /**
     * Replace boolean FALSE with TRUE
     *
     * @param array $tokens
     * @param int $index
     * @return array
     */
    public function getMutation(array $tokens, $index)
    {
        $tokens[$index][0] = T_STRING;
        $tokens[$index][1] = 'true';
        return $tokens;
    }

}
