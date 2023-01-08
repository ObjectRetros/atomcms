<footer
        class="w-full h-14 bg-gray-100 dark:bg-gray-900 mt-auto flex flex-col justify-center md:flex-row md:justify-center text-gray-400 items-center md:px-8 text-sm" onclick="showFooter()">
    <div class="md:font-semibold text-[12px] md:text-[14px] cursor-pointer hover:underline">
        &copy {{ date('Y') }} -
        {{ __(':hotel is a not for profit educational project', ['hotel' => setting('hotel_name')]) }}
    </div>
</footer>

<style>
    .swal2-html-container {
        max-height: 300px;
        overflow-y: scroll;
    }
</style>

<script>
    function showFooter() {
        const creator = '<a class="text-blue-400 underline" href="https://devbest.com/threads/atom-cms-a-multi-theme-cms.93034/" target="_blank">Object</a>';
        const credits = [
            '<strong>Kasja</strong> Helping with design, ideas & GFX <br/>',
            '<strong>Nicollas </strong> Dark mode, Turbolinks, Performance improvements, Article reactions, User sessions, Layout improvements & PT-BR translations <br/>',
            '<strong>Kani</strong> RCON System & Findretros API <br/>',
            '<strong>Dominic</strong> Performance improvements & User sessions <br/>',
            '<strong>Oliver</strong> Profile page & Finnish translations <br/>',
            '<strong>Beny</strong> Findretros API Fixes & CF Fixes <br/>',
            '<strong>Live</strong> French translations, bugfixes & tweaks <br/>',
            '<strong>DamienJolly</strong> Bugfixes <br/>',
            '<strong>Danbo</strong> Bugfixes <br/>',
            '<strong>Diddy/Josh</strong> Code readability improvements <br/>',
            '<strong>Damue</strong> German translations <br/>',
            '<strong>Talion</strong> Turkish translations <br/>',
            '<strong>CentralCee & Rille</strong> Swedish translations <br/>',
            '<strong>Yannick</strong> Netherlands translations <br/>',
            '<strong>Gedomi</strong> Spanish translations <br/>',
            '<strong>Lorenzune</strong> Italian translations <br/>',
            '<strong>Twana</strong> Norwegian translations <br/>'
        ];
        const content = '{{ __('Thank you for playing :hotel. We have put a lot of effort into making the hotel what it is, and we truly appreciate you being here❤️', ['hotel' => setting('hotel_name')]) }}';
        const drivenBy = '{{ __(':hotel is driven by Atom CMS made by:', ['hotel' => setting('hotel_name')]) }}';
        Swal.fire(
            '<span class="text-[26px]">{{ setting('hotel_name') }}</span>',
            `<span class="text-sm">${content}<br/><br/>${drivenBy} ${creator}<br/><br/><span class="flex flex-col space-y-2">Credits:<br/>${credits.join('')}</span></span>`,
            'question'
        )
    }
</script>