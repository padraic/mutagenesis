<?php

require_once 'Mutateme/Mutation.php';

class Mutateme_Mutation_OperatorSubtraction extends Mutateme_Mutation
{

    /**
     * Replace minus sign (-) with plus sign (+)
     *
     * @param array $tokens
     * @param int $index
     * @return array
     */
    public function getMutation(array $tokens, $index)
    {
        $tokens[$index] = '+';
        return $tokens;
    }

}
