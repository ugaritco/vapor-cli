<?php

namespace Ugarit\VaporCli;

use Symfony\Component\Console\Question\ChoiceQuestion;

class KeyChoiceQuestion extends ChoiceQuestion
{
    /**
     * {@inheritdoc}
     */
    protected function isAssoc($array): bool
    {
        return true;
    }
}
