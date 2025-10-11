<?php

namespace App\Models\Community\Staff;

use App\Models\Game\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteOpenPosition extends Model
{
    protected $guarded = ['id'];

    protected $table = 'website_open_positions';

    use HasFactory;

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
            WebsiteStaffApplications::where('rank_id', $openPosition->permission_id)->delete();
        });
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function applications()
    {
        return $this->hasMany(WebsiteStaffApplications::class, 'rank_id', 'permission_id');
    }

    public function scopeCanApply($query)
    {
        return $query->where('apply_from', '<=', now())->where('apply_to', '>', now());
    }
}
