<!doctype html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ setting('hotel_name') }} — {{ __('Maintenance') }}</title>
    <link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.1/dist/flowbite.min.css" />
    <script src="https://unpkg.com/flowbite@1.5.1/dist/flowbite.js"></script>
    @vite(['resources/themes/atom/css/app.css', 'resources/themes/atom/js/app.js'])
</head>
<body class="h-screen overflow-hidden bg-gray-900"
      style="background-image: url({{ asset('assets/images/maintenance/background.png') }}); background-size: cover; background-position: center;">

    <x-messages.flash-messages />

    <x-modals.modal-wrapper classes="flex flex-col justify-center items-center h-full relative">

        @guest
        <div class="absolute top-6 right-6">
            <button @click="open = !open"
                class="text-white bg-black bg-opacity-40 hover:bg-opacity-60 transition ease-in-out duration-200 py-2 px-5 rounded-full font-semibold text-sm backdrop-blur-sm">
                {{ __('Staff login') }}
            </button>
        </div>
        @endguest

        <img src="{{ asset('assets/images/maintenance/pictures.png') }}"
             alt="{{ setting('hotel_name') }}"
             class="max-w-xs mb-6 drop-shadow-xl">

        <div class="text-center px-6 py-5 rounded-2xl bg-black bg-opacity-50 backdrop-blur-sm max-w-md">
            <h1 class="text-3xl font-black text-white mb-2">
                🔧 {{ __('Under Maintenance') }}
            </h1>
            <p class="text-gray-300 text-sm">
                {{ setting('maintenance_message') ?: __('We\'ll be back shortly. Thank you for your patience!') }}
            </p>
        </div>

        <x-modals.regular-modal x-model="show {{ session()->get('wrong-auth') }}">
            <x-auth.login-form />
        </x-modals.regular-modal>

    </x-modals.modal-wrapper>
</body>
</html>
