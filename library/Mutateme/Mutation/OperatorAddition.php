<?php

require_once 'Mutateme/Mutation.php';

class Mutateme_Mutation_OperatorAddition extends Mutateme_Mutation
{

    /**
     * Replace plus sign (+) with minus sign (-)
     *
     * @param array $tokens
     * @param int $index
     * @return array
     */
    public function getMutation(array $tokens, $index)
    {
        $tokens[$index] = '-';
        return $tokens;
    }

}
