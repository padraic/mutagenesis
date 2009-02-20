<?php

require_once 'Mutateme/Mutation.php';

class Mutateme_Mutation_BooleanTrue extends Mutateme_Mutation
{

    /**
     * Replace boolean TRUE with FALSE
     *
     * @param array $tokens
     * @param int $index
     * @return array
     */
    public function getMutation(array $tokens, $index)
    {
        $tokens[$index][0] = T_STRING;
        $tokens[$index][1] = 'false';
        return $tokens;
    }

}
