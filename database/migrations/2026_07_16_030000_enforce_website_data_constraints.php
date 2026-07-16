<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->removeOrphans();
        $this->removeDuplicates('website_article_reactions', ['user_id', 'article_id', 'reaction']);
        $this->removeDuplicates('website_ip_whitelist', ['ip_address']);
        $this->removeDuplicates('website_ip_blacklist', ['ip_address']);
        $this->removeDuplicateTags();

        Schema::table('website_article_reactions', function (Blueprint $table) {
            $table->integer('user_id')->change();
        });

        Schema::table('website_article_reactions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'article_id', 'reaction'], 'article_reactions_user_article_reaction_unique');
        });

        Schema::table('website_shop_articles', function (Blueprint $table) {
            $table->foreign('website_shop_category_id')
                ->references('id')->on('website_shop_categories')
                ->nullOnDelete();
        });

        Schema::table('taggables', function (Blueprint $table) {
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
            $table->unique(['tag_id', 'taggable_type', 'taggable_id'], 'taggables_tag_type_id_unique');
        });

        Schema::table('website_ip_whitelist', function (Blueprint $table) {
            $table->unique('ip_address');
        });

        Schema::table('website_ip_blacklist', function (Blueprint $table) {
            $table->unique('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('website_ip_blacklist', function (Blueprint $table) {
            $table->dropUnique(['ip_address']);
        });

        Schema::table('website_ip_whitelist', function (Blueprint $table) {
            $table->dropUnique(['ip_address']);
        });

        Schema::table('taggables', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
            $table->dropUnique('taggables_tag_type_id_unique');
        });

        Schema::table('website_shop_articles', function (Blueprint $table) {
            $table->dropForeign(['website_shop_category_id']);
        });

        Schema::table('website_article_reactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('article_reactions_user_article_reaction_unique');
        });

        Schema::table('website_article_reactions', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->change();
        });
    }

    private function removeOrphans(): void
    {
        DB::table('website_article_reactions')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('users')
                    ->whereColumn('users.id', 'website_article_reactions.user_id');
            })
            ->delete();

        DB::table('website_shop_articles')
            ->whereNotNull('website_shop_category_id')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('website_shop_categories')
                    ->whereColumn('website_shop_categories.id', 'website_shop_articles.website_shop_category_id');
            })
            ->update(['website_shop_category_id' => null]);

        DB::table('taggables')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('tags')
                    ->whereColumn('tags.id', 'taggables.tag_id');
            })
            ->delete();
    }

    /**
     * @param  list<string>  $columns
     */
    private function removeDuplicates(string $table, array $columns): void
    {
        DB::table($table)
            ->select([...$columns, DB::raw('MAX(id) AS keep_id')])
            ->groupBy($columns)
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->each(function (object $duplicate) use ($columns, $table) {
                $query = DB::table($table);

                foreach ($columns as $column) {
                    $query->where($column, $duplicate->{$column});
                }

                $query->where('id', '!=', $duplicate->keep_id)->delete();
            });
    }

    private function removeDuplicateTags(): void
    {
        DB::table('taggables')
            ->select('tag_id', 'taggable_type', 'taggable_id')
            ->groupBy('tag_id', 'taggable_type', 'taggable_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->each(function (object $duplicate) {
                $attributes = [
                    'tag_id' => $duplicate->tag_id,
                    'taggable_type' => $duplicate->taggable_type,
                    'taggable_id' => $duplicate->taggable_id,
                ];

                DB::table('taggables')->where($attributes)->delete();
                DB::table('taggables')->insert($attributes);
            });
    }
};
