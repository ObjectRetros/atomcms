<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenPosition extends Model
{
    use HasFactory;

    protected $table = 'website_open_positions';

    protected $fillable = [
        'permission_id',
        'description',
        'apply_from',
        'apply_to',
    ];

    protected $casts = [
        'apply_from' => 'datetime',
        'apply_to' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($openPosition) {
            StaffApplication::where('rank_id', $openPosition->permission_id)->delete();
        });
    }

    public function permission()
    {
        return $this->belongsTo(\App\Models\Game\Permission::class, 'permission_id', 'id');
    }

    public function applications()
    {
        return $this->hasMany(StaffApplication::class, 'rank_id', 'permission_id');
    }
}