<div align="center">
<img src="https://i.imgur.com/9ePNdJ4.png" alt="Atom CMS"/>
</div>

<div align="center">
    <a href="https://discord.gg/rX3aShUHdg" target="_blank">
        Join the official Atom CMS Discord server
    </a>
</div>

### 📢 Disclaimer
Please note that Atom CMS is provided as an educational resource for learning purposes only. The creators and contributors to Atom CMS are not responsible for any misuse or unintended consequences arising from the use of Atom CMS. By using Atom CMS, you agree to take full responsibility for your actions and any consequences resulting from your use of Atom CMS. It is your responsibility to ensure that you are using Atom CMS in compliance with all applicable laws and regulations.

### 💙 A Community-Driven Retro CMS
The creation of Atom CMS was driven by the desire to widen the options available for people when choosing a CMS for their new journey. By valuing community input and collaboration, Atom aims to offer a modern and user-friendly system that is easy to understand and work with. Additionally, Atom CMS includes a built-in theme system allowing you the flexibility to use any CSS framework you prefer or even opt for a fully customized vanilla approach. This enables you to tailor the platform to your specific needs and preferences, ensuring a truly unique user experience.

Check out the fantastic trio that brings it all together:
- **PHP (Laravel 11.x)**: At the core of Atom CMS lies Laravel 11.x, a modern and elegant PHP framework that simplifies the development process. Laravel's extensive documentation can be found [here](https://laravel.com/docs/11.x).
- **Vite**: As a next-generation frontend build tool, Vite empowers Atom CMS with lightning-fast development and production speeds. Dive into the Vite documentation [here](https://vitejs.dev/) to explore its full potential.
- **TailwindCSS**: To ensure a visually stunning and responsive design, Atom CMS incorporates TailwindCSS, a utility-first CSS framework. Learn more about TailwindCSS and its installation process from the official [docs](https://tailwindcss.com/docs/installation)

### 🤝 Contributions
We appreciate any type of contribution towards Atom CMS, how ever to maintain a certain standard that make the reviewing process the most streamlined, we have defined some [contribution guidelines](https://github.com/ObjectRetros/atomcms/wiki/0.-Contribution-guidelines). Please take a moment to read them if you're considering contributing towards the CMS, thank you!

### 🧙‍♂️ Learning Laravel
If you are new to Laravel and want to build your own features, we highly recommend the following free resources:

- Laravel Bootcamp: [https://bootcamp.laravel.com/](https://bootcamp.laravel.com/)
- Laravel 11 from Scratch: [https://laracasts.com/series/30-days-to-learn-laravel-11](https://laracasts.com/series/30-days-to-learn-laravel-11)

Laracasts is considered an official learning platform for Laravel, so you can trust the quality of the content and learn best practices from some of the best and most experienced teacher within the field.

### 🤝 Migrating from Another CMS
If you're migrating from another CMS like Cosmic CMS and is unsure what tables to remove or worry about colliding tables names, then fear no more!

Even tho we **highly recommend** to do a proper cleanup yourself, Atom CMS has a built-in option to rename colliding table names and drop matching foreign keys.

All you have to do is to change `RENAME_COLLIDING_TABLES=false` to `RENAME_COLLIDING_TABLES=true` within your `.env`file (You'll get to the .env file in the next step). Once the feature is enabled, Atom CMS will **attempt** to solve any conflicts that might happen due to the use of another CMS.

### 🚧 Requirements
- PHP 8.2 or above [PHP Downloads](https://www.php.net/downloads.php)
- MySQL >= 8.x or  MariaDB >= 10.x
- Composer v2 [Composer Download](https://getcomposer.org/download/)
- NPM (LTS) [Node Download](https://nodejs.org/en/download/)
- An Arcturus Morningstar database [Database repository](https://git.krews.org/morningstar/arcturus-morningstar-base-database)

Please verify the following extensions are enabled inside your php.ini file. If they have a ";" in front of them it means that they're commented out and not enabled. Simply remove the ";" to enable them.
```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=sockets
```

## Setup guide
### 💻 Windows Setup
```shell
git clone https://github.com/ObjectRetros/atomcms.git
cd atomcms
copy .env.example .env (Do not forget to edit the database credentials inside the .env)
composer install
php artisan migrate --seed
php artisan key:generate
npm install && npm run build:atom (For development run: npm run dev:[theme-name] - eg. npm run dev:atom)
```

_Don't forget to link your IIS site to the "public" folder inside for "atomcms"_

**If you are using Atom CMS in production, don't forget to change the following variables:**

```dotenv
APP_ENV=local to APP_ENV=production
APP_DEBUG=true to APP_DEBUG=false
```

#### Required permissions
Please make sure the "atomcms" folder is granted "Full control" for both the IUSR & the IIS_IUSRS.

Here's a GIF of me doing it on a different folder: [https://gyazo.com/7d5f38525a762c1b26bbd7552ca93478](https://gyazo.com/7d5f38525a762c1b26bbd7552ca93478) the principle is the same, you just do it on the "atomhk" folder.

#### cURL error
If you're receiving a cURL 60 error due to eg. setting up findretros, then make sure you download the latest cacert.pem from [https://curl.se/docs/caextract.html](https://curl.se/docs/caextract.html). Once downloaded place it in eg. "C:/" folder and then open your php.ini file, search for `curl.cainfo` uncomment (Remove the ";" infront of the line) it and put the absolute path + file name to your certificate (Eg. "C:/cacert-2022-07-19.pem"). Save the file and your problem should now be solved.

#### Windows Tutorial
Have you always wanted to set up your own hotel from scratch, but are unsure how? Then you can follow my **three** parts series on DevBest which will take you through any step necessary to get everything up and running.

-   Part 1: [https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-1.92532/](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-1.92532/)
-   Part 2: [https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-2.92533/](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-2.92533/)
-   Part 3: [https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-3.92543/](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-3.92543/)


### 🧙‍♂️ Linux Setup
```shell
git clone https://github.com/ObjectRetros/atomcms.git
cd atomcms
cp .env.example .env (Do not forget to edit the database credentials inside the .env)
composer install
php artisan migrate --seed
php artisan key:generate
npm install && npm run build:atom (For development run: npm run dev:[theme-name] (eg. npm run dev:atom))
```

**Grant necessary permissions to used folders. Within your atomcms directory, enter the 4 commands below:**
```shell
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

For NGINX, you can copy the config a base configuration from here: [Deploy a site on nginx](https://laravel.com/docs/9.x/deployment#nginx)

**If you are using Atom CMS in production, don't forget to change the following variables:**
```dotenv
APP_ENV=local to APP_ENV=production
APP_DEBUG=true to APP_DEBUG=false
```

### Testing
AtomCMS contains a small, but growing set of tests. To get started with tests, you have to have a file called `testing.sqlite` in the `database` directory. You can simply create an empty file with that name, and you will be all set!

To run all tests, run `vendor/bin/pest`. You can also use `php artisan test`

### 🔒️ Using HTTPS
In case you're using HTTPs through Cloudflares "Always redirect to HTTPs" feature, you should set `FORCE_HTTPS=` within your `.env` file to `true` this it to make sure everything is properly using HTTPs. This is necessary for some features in Atom CMS to work properly when you're letting cloudflare handle the HTTPs redirects without a dedicated SSL certificate.

### 🔒️ Settings up Cloudflare turnstile captcha
To set up the cloudflare turnstile captcha, to avoid potential bots from mass spamming your hotel, you'll first have to go to [https://www.cloudflare.com/products/turnstile/](https://www.cloudflare.com/products/turnstile/), sign-in or sign-up, next select your cloudflare site & copy the site & secret key to your .env file.

### 🚨 Disable rocket loader
Atom CMS uses Javascript in certain areas, which unfortunately conflicts with Cloudflares Rocket Loader feature. So in-case you have Rocket Loader enabled on Cloudflare, you will have to disable it, otherwise you **will** run into various annoying issues when using Atom CMS.

To disable or check if Rocket Loader is enabled on Cloudflare, all you have to do is, head to your Cloudflare dashboard and find "Speed" -> "Optimization" in the navigation menu, once you've clicked on the "Optimization" menu point, scroll about halfway down until you find "Rocket Loader™" and then un-toggle it in-case it's enabled. That's it! Rocket loader is now disabled, and you're all good to go.

### 📝 Further documentation
Atom comes with its own dedicated documentation site - this makes it a lot easier for you to read about **exactly** what you want, rather than having to read through a giant README file!

As Atom CMS comes packed with **tons** of features, to improve the CMS experience for you and your users it only makes sense to have such a site to make the experience the best possible.

You can find the documentation, addons, and tips & tricks on **[https://github.com/atom-retros/atomcms/wiki](https://github.com/atom-retros/atomcms/wiki)**

### 🔥 Credits
Atom CMS is made possible by the contributions of numerous developers, designers, and community members.

-   **Kasja** - Helping with various design choices, designing Dusk theme, ideas & GFX
-   **Inicollas** - Dark mode, Turbolinks, Performance improvements, Article reactions, User sessions, Layout improvements, PT-BR translations, originally creating the integrated housekeeping.
-   **Kani** - Rcon system & FindRetros API & creating/maintaining Atom CMS v2
-   **Dominic** - Performance improvements & User sessions, generally helping improving the CMS
-   **Beny** - Findretros API fixes & CF Fixes
-   **Live** - French translations, bugfixes & tweaks
-   **MisterDeen** - Custom Discord widget, bugfixes & tweaks
-   **EntenKoeniq** - Automatic language registration, auto color scheme selection, rooms page & profile page fixes,
-   **DuckieTM** - Adjustments to the integreated housekeeping,
-   **DamienJolly** - Bugfixes
-   **Danbo** - Bugfixes
-   **Diddy/Josh** - Code readability improvements
-   **Oliver** - Finnish translations
-   **Damue & EntenKoeniq** - German translations
-   **Talion** - Turkish translations
-   **CentralCee, Rille & Tuborgs** - Swedish translations
-   **Yannick** - Netherland translations
-   **Gedomi** - Spanish translations
-   **Lorenzune** - Italian translations
-   **Twana** - Norwegian translations
-   **Plow** - French translations
-   **Sonay** - Creating material theme
-   **Raizer** - Circinus
