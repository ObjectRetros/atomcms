<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Wordfilter extends Model
{
    use HasFactory;
	use LogsActivity;

    protected $guarded = [];

    protected $table = 'wordfilter';

    protected $primaryKey = 'key';

    public $timestamps = false;

    public $incrementing = false;
	
	public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll([]);
    }
}
