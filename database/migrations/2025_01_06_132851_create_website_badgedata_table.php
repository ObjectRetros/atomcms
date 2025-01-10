<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsiteBadgedataTable extends Migration
{
    public function up()
    {
        Schema::create('website_badgedata', function (Blueprint $table) {
            $table->id();
            $table->string('badge_key')->unique(); // e.g., badge_desc_14X12
            $table->string('badge_name'); // e.g., 14X12
            $table->text('badge_description'); // e.g., "Ohh palmboom, ohhh palmboom, wat zijn je takken wonderschoon!"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_badgedata');
    }
}
