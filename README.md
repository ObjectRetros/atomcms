<div align="center">
<img src="https://i.imgur.com/9ePNdJ4.png" alt="Atom CMS"/>
</div>

<div align="center">
    <a href="https://discord.gg/rX3aShUHdg" target="_blank">
        Join the official Atom CMS Discord server
    </a>
</div>

## What is Atom CMS?
Atom CMS is a Habbo Retro CMS, aiming to provide the best experience possible for you and your users. 

**What technologies is Atom CMS built with?**
- PHP (Laravel 9.x)
  [Laravel docs](https://laravel.com/docs/9.x).
- Vite [Vite docs](https://vitejs.dev/).
- TailwindCSS
[Tailwind docs](https://tailwindcss.com/docs/installation).

You can however use any CSS framework you'd like or if you want to go fully vanilla that possible too, due to the built-in theme system that Atom CMS comes with.

Laravel was chosen as its backend, due to it being very robust and battle tested within "in the real world" on top up that it has a huge community to back it, with tons of free (& paid) learning resources and its solid documentation that other CMS' (& frameworks) normally lack. Combine those things together, and you'll be able to build anything you want even as a beginner, you don't need to be a PHP expert or a frontend master to work with Atom CMS!

**New to Laravel?**
If you are new to Laravel and want to build your own features, then I highly recommend the free bootcamp and two (free) video courses. 
[https://bootcamp.laravel.com/](https://bootcamp.laravel.com/)

- [https://laracasts.com/series/laravel-8-from-scratch](https://laracasts.com/series/laravel-8-from-scratch)
- [https://laracasts.com/series/whats-new-in-laravel-9](https://laracasts.com/series/whats-new-in-laravel-9)

Laracasts is an **official** learning platform for Laravel, so do not worry if you decide to take the time to watch the courses, you'll be taught some of the best practises by some of the best teachers within the Laravel community.

## Why was Atom CMS made?
It's built with the community in mind, meaning we highly value community input, rather than only bringing our own ideas & vision to live & so that rather than it's for us to "show off" how good developers we're, we want everyone to be able to contribute or customise Atom CMS to their needs without having a bachelor in programming.

Another reason for creating Atom CMS is to provide the community with modern & robust CMS, using **industry approved** technologies which is not only easy to understand but also "easy" to work with. Also, we wanted to bring a wider CMS variation to the scene. 

We also included a built-in theme system in Atom CMS so that you can either contribute to the community or customise your own hotel as it becomes a breeze to brew up a new theme in no time.

## Coming from another cms?
If you're coming from another CMS like Cosmic CMS and is unsure what tables to remove or worry about colliding tables names, then fear no more!

Even tho we **highly recommend** to do a proper cleanup yourself, Atom CMS has a built-in option to rename colliding table names and drop matching foreign keys.

All you have to do is to change `RENAME_COLLIDING_TABLES=false` to `RENAME_COLLIDING_TABLES=true` within your `.env`file (You'll get to the .env file in the next step). Once the feature is enabled, Atom CMS will **attempt** to solve any conflicts that might happen due to the use of another CMS. 

## Setup guide
The following requirements is needed to setup Atom CMS:
- PHP 8.1 or above [PHP Downloads](https://www.php.net/downloads.php)
- MySQL 8.x or MariaDB 10.x or newer
- Composer v2 [Composer Download](https://getcomposer.org/download/)
- NPM (LTS) [Node Download](https://nodejs.org/en/download/)
- An Arcturus Morningstar database [Database repository](https://git.krews.org/morningstar/arcturus-morningstar-base-database)

Once all of the above has been installed & setup, you can continue doing the following:
Open CMD (Command Prompt) and navigate into the path you want the CMS to be located at, and run the following commands:

*If you're wondering why there is no SQL files for Atom CMS, then it's because Laravel offers something called "migrations" which will automatically create all the tables needed for Atom CMS to work*

#### Required extensions
Please verify the following extensions are enabled inside your php.ini file. If they have a ";" in front of them it means that they're commented out and not enabled. Simply remove the ";" to enable them.
```
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=sockets
```

#### Windows Setup
```
[Https] git clone https://github.com/ObjectRetros/atomcms.git
cd atomcms
copy .env.example .env (Don't forget to edit the database credentials inside the .env)
composer install 
npm install && npm run build:atom (For development run: npm run dev:[theme-name] - eg. npm run dev:atom)
php artisan key:generate
php artisan migrate --seed
```

*Don't forget to link your IIS site to the "public" folder inside for "atomcms"*

**If you are using Atom CMS in production, don't forget to change the following variables:**
```
APP_ENV=local to APP_ENV=production
APP_DEBUG=true to APP_DEBUG=false
```

#### Required permissions
Please make sure the "atomcms" folder is granted "Full control" for both the IUSR & the IIS_IUSRS.

Here's a GIF of me doing it on a different folder: [https://gyazo.com/7d5f38525a762c1b26bbd7552ca93478](https://gyazo.com/7d5f38525a762c1b26bbd7552ca93478) the principle is the same, you just do it on the "atomhk" folder.

#### cURL error
If you're receiving a cURL 60 error due to eg. setting up findretros, then make sure you download the latest cacert.pem from [https://curl.se/docs/caextract.html](https://curl.se/docs/caextract.html). Once downloaded place it in eg. "C:/" folder and then open your php.ini file, search for ``curl.cainfo`` uncomment (Remove the ";" infront of the line) it and put the absolute path + file name to your certificate (Eg. "C:/cacert-2022-07-19.pem"). Save the file and your problem should now be solved.

#### Windows Tutorial
Have you always wanted to set up your own hotel from scratch, but are unsure how? Then  you can follow my **three** parts series on DevBest which will take you through any step necessary to get everything up and running.

- Part 1: [https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-1.92532/](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-1.92532/)
- Part 2: [https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-2.92533/](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-2.92533/)
- Part 3: [https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-3.92543/](https://devbest.com/threads/how-to-set-up-a-retro-in-2022-iis-nitro-html5-part-3.92543/)

#### Linux Setup
```
[Https] git clone https://github.com/ObjectRetros/atomcms.git
cd atomcms
cp .env.example .env (Don't forget to edit the database credentials inside the .env)
composer install
npm install && npm run build:atom (For development run: npm run dev:[theme-name] (eg. npm run dev:atom))
php artisan key:generate
php artisan migrate --seed
```

**Grant necessary permissions to used folders. Within your atomcms directory, enter the 4 commands below:**
```
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

For NGINX, you can copy the config a base configuration from here: [Deploy a site on nginx](https://laravel.com/docs/9.x/deployment#nginx)

**If you are using Atom CMS in production, don't forget to change the following variables:**
```
APP_ENV=local to APP_ENV=production
APP_DEBUG=true to APP_DEBUG=false
```

## Using HTTPS
In case you're using HTTPs through Cloudflares "Always redirect to HTTPs" feature, you should set `FORCE_HTTPS=` within your `.env` file to `true` this it to make sure everything is properly using HTTPs. This is necessary for some features in Atom CMS to work properly when you're letting cloudflare handle the HTTPs redirects without a dedicated SSL certificate.

## Disable rocket loader
Atom CMS uses Javascript in certain areas, which unfortunately conflicts with Cloudflares Rocket Loader feature. So in-case you have Rocket Loader enabled on Cloudflare, you will have to disable it, otherwise you **will** run into various annoying issues when using Atom CMS.

To disable or check if Rocket Loader is enabled on Cloudflare, all you have to do is, head to your Cloudflare dashboard and find "Speed" -> "Optimization" in the navigation menu, once you've clicked on the "Optimization" menu point, scroll about halfway down until you find "Rocket Loader™" and then un-toggle it in-case it's enabled. That's it! Rocket loader is now disabled, and you're all good to go.

## Feature-addons
Atom comes with its own dedicated documentation site - this makes it a lot easier for you to read about **exactly** what you want, rather than having to read through a giant README file!

As Atom CMS comes packed with **tons** of features, to improve the CMS experience for you and your users it only makes sense to have such a site to make the experience the best possible.

You can find the documentation, addons, and tips & tricks on **[https://retros.guide/docs/category/atom-cms](https://retros.guide/docs/category/atom-cms)**

## Credits
- **Kasja** - Helping with design, ideas & GFX
- **Nicollas** - Dark mode, Turbolinks, Performance improvements, Article reactions, User sessions, Layout improvements & PT-BR translations
- **Dominic** - Performance improvements & User sessions
- **Oliver** - Profile page & Finnish translations
- **Beny** - Findretros API fixes & Cloudflare Fixes
- **Live** - French translations, bugfixes & tweaks
- **MisterDeen** - Custom Discord widget, bugfixes & tweaks
- **DamienJolly** - Bugfixes
- **Danbo** - Bugfixes
- **Diddy/Josh** - Code readability improvements
- **Damue** - German translations
- **Talion** - Turkish translations
- **CentralCee & Rille** - Swedish translations
- **Yannick** - Netherland translations
- **Gedomi** - Spanish translations
- **Lorenzune** - Italian translations
- **Twana** - Norwegian translations
- **Kani** - Rcon System & Findretros API
- **Sonay** - Material theme
- **Raizer** - Circinus
