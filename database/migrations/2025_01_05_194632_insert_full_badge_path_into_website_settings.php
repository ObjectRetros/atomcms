<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertFullBadgePathIntoWebsiteSettings extends Migration
{
    public function up()
    {
        DB::table('website_settings')->insert([
            'key' => 'FullBadgePath',
            'value' => '/var/www/gamedata/c_images/album1584',
            'comment' => 'This is the default path for the badges for uploading, so this needs the full path',
        ]);
    }

    public function down()
    {
        DB::table('website_settings')->where('key', 'FullBadgePath')->delete();
    }
}
