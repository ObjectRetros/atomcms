<?php

namespace App\Policies;

class WordfilterPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_wordfilter';
    }
}
