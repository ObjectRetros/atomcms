<?php

namespace App\Models;

use App\Models\Compositions\HasBadge;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model implements HasBadge
{
    public $timestamps = false;

    protected $guarded = [];

    public function getBadgePath(): string
    {
        return sprintf('%sACH_%s.gif', setting('badges_path'), $this->getBadgeName());
    }

    public function getBadgeName(): string
    {
        return sprintf('%s%s', $this->name, (string) $this->level);
    }
}
