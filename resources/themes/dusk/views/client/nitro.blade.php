<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ setting('hotel_name') }} - Nitro</title>

    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Condensed&display=swap" rel="stylesheet">

    @vite(['resources/themes/' .  setting('theme') . '/css/app.css', 'resources/themes/' .  setting('theme') . '/js/app.js'], 'build')
</head>

<body class="overflow-hidden" id="nitro-client">
<div class="absolute top-4 left-4 z-10 flex gap-x-2">
    <a data-turbolinks="false" href="{{ route('me.show') }}">
        <x-client.client-button>
            <x-icons.home />
        </x-client.client-button>
    </a>

    <div onclick="reloadClient()">
        <x-client.client-button>
            <x-icons.reload />
        </x-client.client-button>
    </div>

    <div onclick="toggleFullscreen()">
        <x-client.client-button>
            <x-icons.fullscreen />
        </x-client.client-button>
    </div>

    <x-client.client-button classes="flex items-center justify-center gap-x-1">
        <x-icons.user />

        <span id="online-count"></span>
    </x-client.client-button>
</div>
<iframe id="nitro" src="{{ sprintf('%s/index.html?sso=%s', setting('nitro_path'), $sso) }}"
        class="absolute top-0 left-0 m-0 h-full w-full overflow-hidden border-none p-0"></iframe>

{{-- Show disconnected message on client if the user has been disconnected --}}
<div id="disconnected" class="h-screen w-full">
    <div class="absolute h-full w-full bg-black/50"></div>

    <div class="relative flex h-full w-full flex-col items-center justify-center gap-4">
        <h2 class="text-2xl text-white">
            {{ __('Whoops! It seems like you have been disconnected...') }}
        </h2>

        <div class="flex gap-x-4">
            <button
                class="py-2 px-4 text-white rounded bg-[#eeb425] hover:bg-[#e3aa1e] border-2 border-[#cf9d15] transition ease-in-out"
                onclick="reloadClient()">
                {{ __('Reload client') }}
            </button>

            <a href="{{ route('me.show') }}">
                <x-form.secondary-button>
                    {{ __('Back to website') }}
                </x-form.secondary-button>
            </a>
        </div>
    </div>
</div>

<script>
    function toggleFullscreen() {
        if (document.fullscreenElement) {
            document.exitFullscreen();

            return;
        }

        document.documentElement.requestFullscreen();
    }

    function reloadClient() {
        window.location.href = window.location;
    }

    window.addEventListener('DOMContentLoaded', () => {
        function getOnlineUserCount() {
            fetch('{{ route('api.online-count') }}')
                .then(function(response) {
                    return response.json();
                })
                .then(function(response) {
                    document.getElementById('online-count').innerHTML = response.data.onlineCount;
                });
        }

        getOnlineUserCount();

        setInterval(function() {
            getOnlineUserCount();
        }, 15000);
    });
</script>

<script src="{{ asset('assets/js/atom.js') }}"></script>
</body>

</html>
