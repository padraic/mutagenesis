<?php

require_once 'Mutateme/Mutation.php';

class Mutateme_Mutation_OperatorIncrement extends Mutateme_Mutation
{
    /**
     * Replace T_INC (++) with T_DEC (--)
     *
     * @param array $tokens
     * @param int $index
     * @return array
     */
    public function getMutation(array $tokens, $index)
    {
        $tokens[$index][0] = T_DEC;
        $tokens[$index][1] = '--';
        return $tokens;
    }

}
