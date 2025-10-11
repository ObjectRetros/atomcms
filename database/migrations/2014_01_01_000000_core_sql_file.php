<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class CoreSqlFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only run in testing environment
        if (app()->environment() !== 'testing') {
            return;
        }

        ini_set('memory_limit', '-1');

        if (! Schema::hasTable('users')) {
            $output = new ConsoleOutput;
            $output->writeln("\n🚀 Running Core SQL File Migration...");

            // Set charset to support emojis
            DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');

            $sqlFile = database_path('migrations/sqls/default.sql');
            $sql = file_get_contents($sqlFile);

            // Split into individual statements to show progress
            $statements = array_filter(
                preg_split('/;\s*\n/', $sql),
                fn ($stmt) => ! empty(trim($stmt)) &&
                           ! str_starts_with(trim($stmt), '--') &&
                           ! str_starts_with(trim($stmt), '/*'),
            );

            $totalStatements = count($statements);
            $output->writeln("📊 Processing $totalStatements SQL statements...");

            $progressBar = new ProgressBar($output, $totalStatements);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $progressBar->setMessage('Starting...');
            $progressBar->start();

            foreach ($statements as $index => $statement) {
                $statement = trim($statement);

                if (! empty($statement)) {
                    try {
                        DB::unprepared($statement);

                        // Update progress message based on statement type
                        if (str_contains($statement, 'CREATE TABLE')) {
                            preg_match('/CREATE TABLE\s+`?(\w+)`?/i', $statement, $matches);
                            $tableName = $matches[1] ?? 'unknown';
                            $progressBar->setMessage("Created table: $tableName");
                        } elseif (str_contains($statement, 'INSERT INTO')) {
                            $progressBar->setMessage('Inserting data...');
                        } else {
                            $progressBar->setMessage('Processing...');
                        }

                    } catch (\Exception $e) {
                        $progressBar->setMessage('Error: ' . substr($e->getMessage(), 0, 50) . '...');
                        // Continue on non-critical errors
                    }
                }

                $progressBar->advance();

                // Small delay to make progress visible
                if ($index % 100 === 0) {
                    usleep(1000); // 1ms
                }
            }

            $progressBar->setMessage('✅ Complete!');
            $progressBar->finish();
            $output->writeln("\n🎉 Core SQL schema imported successfully!");
        } else {
            $output = new ConsoleOutput;
            $output->writeln('ℹ️  Core tables already exist, skipping SQL import.');
        }
    }
}
