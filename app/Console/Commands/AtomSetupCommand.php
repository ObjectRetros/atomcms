<?php

namespace App\Console\Commands;

use App\Models\Miscellaneous\WebsiteSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AtomSetupCommand extends Command
{
    protected $signature = 'atom:setup {--auto=false}';

    protected $description = 'Takes you through a basic setup, allowing you to define general settings';

    private function progressInfo(int $step): void
    {
        $this->info(sprintf('Step %s/13', $step));
        $this->newLine();
    }

    public function handle(): void
    {
        Artisan::call('db:seed --class=WebsiteSettingsSeeder');

        if ($this->option('auto') === 'false') {
            $step = 1;

            $this->progressInfo($step);
            $step++;

            $hotelName = $this->ask('Enter your hotel name');
            WebsiteSetting::where('key', '=', 'hotel_name')->update([
                'value' => empty($hotelName) ? 'Hotel' : $hotelName,
            ]);

            $this->progressInfo($step);
            $step++;

            $colorMode = $this->choice('Enter your preferred CMS color mode', ['light', 'dark'], 0);
            WebsiteSetting::where('key', '=', 'cms_color_mode')->update([
                'value' => $colorMode,
            ]);

            $this->progressInfo($step);
            $step++;

            $startCredits = $this->ask('Enter the amount of credits new users should start with: (default is 5000)');
            WebsiteSetting::where('key', '=', 'start_credits')->update([
                'value' => empty($startCredits) ? '5000' : $startCredits,
            ]);

            $this->progressInfo($step);
            $step++;

            $startDuckets = $this->ask('Enter the amount of credits new users should start with: (default is 5000)');
            WebsiteSetting::where('key', '=', 'start_duckets')->update([
                'value' => empty($startDuckets) ? '5000' : $startDuckets,
            ]);

            $this->progressInfo($step);
            $step++;

            $startDiamonds = $this->ask('Enter the amount of diamonds new users should start with: (default is 100)');
            WebsiteSetting::where('key', '=', 'start_diamonds')->update([
                'value' => empty($startDiamonds) ? '100' : $startDiamonds,
            ]);

            $this->progressInfo($step);
            $step++;

            $startPoints = $this->ask('Enter the amount of points new users should start with (default is 0)');
            WebsiteSetting::where('key', '=', 'start_points')->update([
                'value' => empty($startPoints) ? '0' : $startPoints,
            ]);

            $this->progressInfo($step);
            $step++;

            $maxAccountsPerIP = $this->ask('Enter the amount of accounts a user can register per IP address (default is 2)');
            WebsiteSetting::where('key', '=', 'max_accounts_per_ip')->update([
                'value' => empty($maxAccountsPerIP) ? '2' : $maxAccountsPerIP,
            ]);

            $this->progressInfo($step);
            $step++;

            $recaptchaEnabled = $this->choice('Google ReCaptcha enabled: (Do not forget to add your keys to your .env file in-case you set this to 1)', ['0', '1'], 0);
            WebsiteSetting::where('key', '=', 'google_recaptcha_enabled')->update([
                'value' => $recaptchaEnabled,
            ]);

            $this->progressInfo($step);
            $step++;

            $wordfilterEnabled = $this->choice('CMS wordfilter enabled', ['0', '1'], 1);
            WebsiteSetting::where('key', '=', 'website_wordfilter_enabled')->update([
                'value' => $wordfilterEnabled,
            ]);

            $this->progressInfo($step);
            $step++;

            $requiredBetaCode = $this->choice('Requires beta code to register', ['0', '1'], 0);
            WebsiteSetting::where('key', '=', 'requires_beta_code')->update([
                'value' => $requiredBetaCode,
            ]);

            $this->progressInfo($step);
            $step++;

            $registrationDisabled = $this->choice('Disable registration (Can be re-enabled later inside website_settings table if set to 1)', ['0', '1'], 0);
            WebsiteSetting::where('key', '=', 'disable_registration')->update([
                'value' => $registrationDisabled,
            ]);

            $this->progressInfo($step);
            $step++;

            $giveHC = $this->choice('Give all new users HC automatically', ['0', '1'], 0);
            WebsiteSetting::where('key', '=', 'give_hc_on_register')->update([
                'value' => $giveHC,
            ]);

            $this->progressInfo($step);

            $maxCommentArticles = $this->ask('Enter the amount of comments each user can post per article (default is 2)');
            WebsiteSetting::where('key', '=', 'max_comment_per_article')->update([
                'value' => empty($maxCommentArticles) ? '2' : $maxCommentArticles,
            ]);
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
    }
}
