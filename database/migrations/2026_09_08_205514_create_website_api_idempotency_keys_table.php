<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_api_idempotency_keys', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('operation', 40);
            $table->string('key_hash', 64);
            $table->string('request_hash', 64);
            $table->uuid('operation_reference');
            $table->json('response')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['user_id', 'operation', 'key_hash'], 'website_api_idempotency_actor_operation_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_api_idempotency_keys');
    }
};
