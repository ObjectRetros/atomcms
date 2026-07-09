<?php

namespace App\Policies;

class TagPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_article_tags';
    }
}
