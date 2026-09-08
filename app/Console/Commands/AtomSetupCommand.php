<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewUser;
use App\Emulator\Contracts\RankRepository;
use App\Models\User;
use App\Services\InstallationSetup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AtomSetupCommand extends Command
{
    protected $signature = 'atom:setup {--auto=false} {--complete : Finish configuration without the browser wizard} {--settings= : JSON file of essential hotel settings} {--admin= : Existing username to promote, or username to create with protected password input}';

    protected $description = 'Takes you through a basic setup, allowing you to define general settings';

    public function handle(): int
    {
        if ($this->option('complete')) {
            return $this->completeHeadlessSetup();
        }

        Artisan::call('db:seed --class=WebsiteSettingsSeeder');

        if ($this->option('auto') === 'false') {
            $values = [];
            $setup = app(InstallationSetup::class);
            foreach ($setup->rules() as $key => $rules) {
                $label = ucwords(str_replace('_', ' ', $key));
                $default = (string) setting($key, '');
                $choices = null;
                foreach ($rules as $rule) {
                    if (str_starts_with($rule, 'in:')) {
                        $choices = explode(',', substr($rule, 3));
                    }
                }
                $values[$key] = $choices === null
                    ? $this->ask($label, $default)
                    : $this->choice($label, $choices, in_array($default, $choices, true) ? $default : $choices[0]);
            }
            $setup->configure($values);
        }

        $seeders = [
            'WebsiteLanguageSeeder',
            'WebsiteArticleSeeder',
            'WebsitePermissionSeeder',
            'WebsiteWordfilterSeeder',
            'WebsiteTeamSeeder',
            'WebsiteRuleCategorySeeder',
            'WebsiteRuleSeeder',
        ];

        foreach ($seeders as $seeder) {
            Artisan::call(sprintf('db:seed --class=%s', $seeder));
        }

        $this->info('The setup was successful!');

        return self::SUCCESS;
    }

    private function completeHeadlessSetup(): int
    {
        try {
            $setup = app(InstallationSetup::class);
            $settings = [];
            if ($path = $this->option('settings')) {
                $contents = file_get_contents((string) $path);
                if (! is_string($contents)) {
                    throw new \RuntimeException('Unable to read the settings file.');
                }
                $settings = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
                if (! is_array($settings)) {
                    throw new \InvalidArgumentException('Settings must be a JSON object.');
                }
            } elseif ($this->input->isInteractive()) {
                $settings['hotel_name'] = $this->ask('Hotel name', setting('hotel_name') ?: 'Hotel');
            }
            $setup->configure($settings);
            $admin = $this->option('admin');
            $hasUsers = User::query()->exists();
            if (! $admin && ! $hasUsers && $this->input->isInteractive()) {
                $admin = $this->ask('Username for the first administrator');
            }
            if (! $admin && ! $hasUsers) {
                throw new \InvalidArgumentException('Specify --admin for an empty hotel. Supply ATOM_ADMIN_EMAIL and ATOM_ADMIN_PASSWORD in protected environment input.');
            }
            if (is_string($admin) && $admin !== '') {
                $user = User::where('username', $admin)->first();
                if ($user === null) {
                    $mail = getenv('ATOM_ADMIN_EMAIL') ?: null;
                    $password = getenv('ATOM_ADMIN_PASSWORD') ?: null;
                    if ($this->input->isInteractive()) {
                        $mail = $this->ask('Administrator email', $mail);
                        $password = $this->secret('Administrator password');
                    }
                    $user = app(CreateNewUser::class)->createAdministrator([
                        'username' => $admin, 'mail' => $mail, 'password' => $password, 'password_confirmation' => $password,
                    ]);
                } else {
                    $user->forceFill(['rank' => app(RankRepository::class)->highestRank()])->save();
                }
                $this->info('Administrator configured: ' . $user->username);
            }
            $setup->complete();
            $this->info('Installation complete. Housekeeping is available at /housekeeping.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
