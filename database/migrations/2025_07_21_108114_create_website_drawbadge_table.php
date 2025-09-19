<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_drawbadges', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('badge_path');
            $table->string('badge_url');
            $table->string('badge_name');
            $table->string('badge_desc');
            $table->boolean('published')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        DB::table('website_settings')->insert([
            [
                'key' => 'drawbadge_currency_value',
                'value' => '150',
                'comment' => 'Adjust here the amount that is required for buying a custom badge',
            ],
            [
                'key' => 'drawbadge_currency_type',
                'value' => 'credits',
                'comment' => 'Adjust here the currency type (credits, duckets, diamonds, points)',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_drawbadges');

        DB::table('website_settings')->whereIn('key', [
            'drawbadge_currency_value',
            'drawbadge_currency_type'
        ])->delete();
    }
};