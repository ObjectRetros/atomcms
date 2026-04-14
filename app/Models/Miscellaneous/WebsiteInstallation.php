<?php

namespace App\Models\Miscellaneous;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $step
 * @property int $completed
 * @property string|null $installation_key
 * @property string|null $user_ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation whereInstallationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation whereStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteInstallation whereUserIp($value)
 *
 * @mixin \Eloquent
 */
class WebsiteInstallation extends Model
{
    protected $table = 'website_installation';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
