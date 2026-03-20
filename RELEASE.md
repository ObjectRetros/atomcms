## Atom CMS is now running **Laravel 13** 🎉

We've upgraded the core stack to **Laravel 13**, **Livewire 4**, and **TailwindCSS v4**. This update also consists of various new features, plenty of bug fixes, refactors, and dependency upgrades under the hood. Going forward, we'll be providing more detailed release notes with each update.

### Changelog

**Framework & Dependencies**
- chore: upgrade to Laravel 13, Pest 4, and PHPUnit 12 ([`ea5bd06`](https://github.com/ObjectRetros/atomcms/commit/ea5bd06))
- feat: upgrade Tailwind to v4 ([`024dda1`](https://github.com/ObjectRetros/atomcms/commit/024dda1))
- feat: add Laravel Boost ([`3e276e2`](https://github.com/ObjectRetros/atomcms/commit/3e276e2))

**🤖 What is Laravel Boost?**                                                                                                                                                                                                              
[Laravel Boost](https://github.com/laravel/boost) is an MCP server designed to supercharge AI-assisted development. If you're using AI coding agents like Claude Code, Codex, Cursor etc. Boost gives them direct access to your app's database schema, error logs, route definitions, and version-specific documentation — so they can write better, more accurate code with less back-and-forth.                                                                                    
                                                                                                                                                                                                                                           
To get started, run:                                                                                                                                                                                                                       
```bash                                                                                                                                                                                                                                    
php artisan boost:install                                                                                                                                                                                                                  
```                                                                                                                                                                                                                                        
This will configure the MCP server for your preferred AI tool. From there, your agent can query your database, search Laravel docs for the exact package versions you're using, and debug issues faster. 

**Features**
- feat: Catalog Editor ([`847879c`](https://github.com/ObjectRetros/atomcms/commit/847879c))
- refactor: convert article comments and reactions to Livewire components ([`86880bb`](https://github.com/ObjectRetros/atomcms/commit/86880bb))
- Updated Open Position / Added Teams and some code fixes ([`ceb39d7`](https://github.com/ObjectRetros/atomcms/commit/ceb39d7))

**Bug Fixes & Refactors**
- fix: add default theme fallback in Vite asset loading ([`ad05779`](https://github.com/ObjectRetros/atomcms/commit/ad05779))
- fix: rank uniqueness validation in Open Staff Positions ([`b3fc51d`](https://github.com/ObjectRetros/atomcms/commit/b3fc51d))
- refactor: caching of website settings ([`964226f`](https://github.com/ObjectRetros/atomcms/commit/964226f))
- refactor(profile): improve responsive grid layout and visual structure ([`14e9bd4`](https://github.com/ObjectRetros/atomcms/commit/14e9bd4))
- refactor: replace img avatar with div background & include motto in staff query ([`76d9035`](https://github.com/ObjectRetros/atomcms/commit/76d9035))

### ⚠️ Important: Clean install recommended

Since there have been a lot of dependency changes, we recommend deleting your `node_modules` and `vendor` directories before updating. Then run:

```bash
composer install && npm install && npm run build <theme>
```

Full commit history: https://github.com/ObjectRetros/atomcms/commits/dev/

As always, if you run into any issues don't hesitate to reach out via the appropriate channels!
