<?php

namespace Database\Seeders;

use App\Models\Help\WebsiteHelpCenterCategory;
use Illuminate\Database\Seeder;

class WebsiteHelperCenterCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => config('habbo.site.default_name') . ' Way',
                'content' => 'Welcome to the ' . config('habbo.site.default_name') . " Hotel, where fun and excitement are always around the corner! 🎉 To ensure that everyone has the best experience possible, we've created The " . config('habbo.site.default_name') . " Way - our special set of rules that keeps our community safe and friendly. 🌈<br/><br/>It's important to know that these rules and regulations can change without notice. As a member of our awesome " . config('habbo.site.default_name') . " community, you agree to follow these terms and conditions. 😊 If you don't, there may be sanctions applied to your account.<br/><br/>But don't worry! If you have any questions or concerns about The " . config('habbo.site.default_name') . ' Way, our friendly Hotel Staff are always here to help. 💬 Now, go ahead and click the button below to read The ' . config('habbo.site.default_name') . ' Way and join us in creating a fantastic environment for all! 🚀',
                'image_url' => 'safety_tips_1.png',
                'button_text' => config('habbo.site.default_name') . ' Way',
                'button_url' => '/help-center/rules',
                'position' => 1,
            ],
            [
                'name' => 'Ban Appeals',
                'content' => "If you think you've been unfairly banned from our super cool hotel, no worries - we're here to help! 🌟 All you need to do is submit a ticket 🎟️ and let us know what happened.<br/><br/>We'll check it out and give you a fair chance to return to the awesome world of " . config('habbo.site.default_name') . "! 🕺💃 So go ahead, share your side of the story, and let's get you back in on the fun! 😄",
                'image_url' => 'safety_tips_5.png',
                'button_text' => 'Submit a ban appeal',
                'button_url' => '/help-center/tickets/create',
                'position' => 2,
            ],
            [
                'name' => 'VPN Unblock',
                'content' => "We know that sometimes you might need to use a VPN or proxy connection while visiting our fantastic hotel! 🏨 But since we want to keep our community safe and free from toxicity, we block these connections by default. 🛡️<br/><br/>However, we understand that there are exceptions, and we're here to help! 🌟 If you find yourself in one of these situations, you can request VPN unblocking:<br/><br/><div style='margin-left: 15px;'><strong>1.</strong> You're not using a VPN but still got flagged somehow. 🚩<br/><strong>2.</strong> You're at school or university and need a VPN to access " . config('habbo.site.default_name') . ". 🏫<br /><strong>3.</strong> You're on public connections that might be flagged as a VPN.</div> 📱<br/><br/>Please note that if using a VPN is optional for you, we usually deny the request. This is just to make sure we maintain a positive and friendly environment for all our users! 😄<br/><br/>To request VPN unblocking, simply submit a ticket with an explanation of your situation, and we'll do our best to help you out! Together, let's keep the " . config('habbo.site.default_name') . ' Hotel experience amazing for everyone! 🎉',
                'image_url' => 'safety_tips_2.png',
                'button_text' => 'Submit Unblock request',
                'button_url' => '/help-center/tickets/create',
                'position' => 3,
            ],
            [
                'name' => 'Scam Reports',
                'content' => 'Hey ' . config('habbo.site.default_name') . " buddies! 🎉 We know that sometimes, unfortunately, users might try to scam others out of their coins, diamonds, or furniture. 😢 But don't worry, we've got your back! We don't tolerate this kind of behavior, and we're here to help you report it. 🌟<br/><br/>Have you been scammed? 😨<br/><br/>If so, we're here to assist! Just follow the template below and include it in a ticket under the 'Scam Reports' option:<br/><br/><div style='margin-left: 15px;'><strong>1.</strong> Scammed by:<br/><strong>2.</strong> Date of Scam:<br/><strong>3.</strong> Items Scammed:<br/><strong>4.</strong> Evidence (if available):</div><br/><br/>Remember, it's important to be honest and true to yourself! 🌈 Nobody likes a trickster, and stealing won't make you rich - it makes you a criminal. 😔 By reporting scams, we can work together to keep the " . config('habbo.site.default_name') . ' Hotel a fun, safe, and amazing place for everyone! 🎊',
                'image_url' => 'safety_tips_7.png',
                'button_text' => 'Submit scam report',
                'position' => 4,
            ],
            // Left side boxes
            /*
            *  [
                'name' => "GOTW Rules",
                'content' => "GOTW, or Gamer of the Week is all about celebrating the top events players from the past seven days. 🏆 For every hotel alerted event you win, you'll earn both GOTW points and diamonds! 💎 So put your gaming skills to the test and aim for the top!<br/><br/>Curious about the rules? No problem! Click the link below & you'll find all the information you need to become a GOTW pro.<br/><br/>Get ready to shine as the next Gamer of the Week! 🚀",
                'button_text' => "GOTW Rules",
                'position' => 1,
                'small_box' => true,
            ],
            * */

            [
                'name' => 'Room Ads',
                'content' => 'Hey there, ' . config('habbo.site.default_name') . " friends! 🌟 Did you know that you can make your room even cooler by displaying images in it? 😮 That's right! With room background furniture, you can embed images directly into your room on the hotel! 🖼️<br/><br/>However, by default, you won't have the furniture access or permission to do this. But no worries, you can apply for it! 🎉 All you need to do is submit a ticket and let us know why you'd like to have these awesome room ad rights. 📝<br/><br/>Once you've got the permission, you'll be able to customize your room and make it truly one-of-a-kind! 🌈 So go ahead and tell us why you need those rights, and let's take your room to the next level together! 🚀",
                'button_text' => 'Submit a ticket',
                'button_url' => '/help-center/tickets/create',
                'position' => 1,
                'small_box' => true,
            ],
            [
                'name' => 'Something else?',
                'content' => "We know that sometimes you might have questions, concerns, or just need a little help that doesn't fit into any specific category. No worries, we've got you covered! 🌟<br/><br/>For anything else that you need assistance with, simply select the 'Other' option when submitting a ticket. 📝<br/><br/>Our friendly and helpful Hotel Staff will be more than happy to get in touch and provide the support you need. 🤗 So go ahead, reach out to us for any reason, big or small, and let's make your " . config('habbo.site.default_name') . ' experience the best it can be! 🚀',
                'button_text' => 'Submit a ticket',
                'button_url' => '/help-center/tickets/create',
                'position' => 2,
                'small_box' => true,
            ],
        ];

        foreach ($categories as $category) {
            $attributes = [];

            foreach ($category as $key => $value) {
                $attributes[$key] = $value;
            }

            WebsiteHelpCenterCategory::updateOrCreate(['name' => $category['name']], $attributes);
        }
    }
}
