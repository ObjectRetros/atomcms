<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasColumn('users', 'paypal_api_token')) {
            Schema::dropColumns('users','paypal_api_token');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('paypal_api_token')->after('password')->unique()->nullable()->default(null);
        });

        $users = User::query()->whereNull('paypal_api_token')->get();

        foreach ($users as $user) {
            $user->update([
                'paypal_api_token' => Str::uuid(),
            ]);
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['api_token']);
        });
    }
};