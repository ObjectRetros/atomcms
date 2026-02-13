<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ setting('hotel_name') }} - {{ __('Maintenance') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/flowbite.min.css') }}"/>
    <script src="{{ asset('assets/vendor/js/flowbite.js') }}"></script>

    @vite(['resources/themes/' .  setting('theme') . '/css/app.css', 'resources/themes/' .  setting('theme') . '/js/app.js'], 'build')
</head>

<body class="h-screen overflow-hidden relative bg-[#233143]">
<x-messages.flash-messages/>

<div class="w-full h-full flex">
    <div class="bg-[#111827] w-96 h-full py-10 hidden lg:flex lg:flex-col items-center gap-10 relative px-6">
        <div>
            <img src="{{ setting('cms_logo') }}" alt="">
        </div>

        <div class="flex flex-col gap-2 w-full relative z-10">
            @foreach($tasks as $task)
                <div
                    class="relative h-[80px] w-full overflow-hidden bg-[#233143] py-2 pr-2 transition duration-150 ease-in-out hover:scale-[101%] text-gray-100">
                    <div class="flex h-full w-full items-center gap-x-6">
                        <div>
                            <img
                                class="drop-shadow-thin -mb-8 transition duration-300 ease-in-out"
                                style="image-rendering: auto"
                                src="{{ setting('avatar_imager') }}{{ $task->user->look }}&direction=3&head_direction=3&gesture=sml&action=wav&frame=0"
                                alt=""/>
                        </div>

                        <div class="flex h-full w-2/3 items-center break-words">
                            {{ $task->task }}
                        </div>
                    </div>

                    <div class="absolute bottom-2 right-2 flex justify-between w-full">
                        <small class="pl-24">{{ __('By: :user', ['user' => $task->user?->username]) }}</small>
                        @if($task->completed)
                            <small
                                class="flex gap-1 items-center">
                                Status:
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </small>
                        @else
                            <small
                                class="flex gap-1 items-center">
                                Status:
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-yellow-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </small>
                        @endif
                    </div>
                </div>
            @endforeach

            {{ $tasks->links() }}
        </div>

        <div class="absolute bottom-0 right-0 hidden lg:block z-0">
            <img src="/assets/images/maintenance/fireman.png" alt="">
        </div>
    </div>

    <div class="px-4 lg:px-14 text-gray-100 h-full flex flex-col justify-center relative z-10">
	
        <h2 class="text-4xl lg:text-5xl font-bold uppercase">Maintenance break!</h2>

        <article class="mt-4 text-lg lg:text-xl max-w-[600px] text-wrap">
            <p>{!! setting('maintenance_message') !!}</p>
        </article>
    </div>
</div>

<img class="absolute bottom-0 right-0 opacity-60 hidden lg:block z-0" src="/assets/images/maintenance/hotelview.png" alt="">

@guest
    <div class="absolute top-6 right-6 z-50">
        <x-modals.modal-wrapper>
            <button @click="open = !open"
                    class="rounded-full bg-white/70 px-4 py-2 font-semibold text-black transition duration-200 ease-in-out hover:bg-white">
                {{ __('Staff login') }}
            </button>

            <x-modals.regular-modal x-model="show {{ session()->get('wrong-auth') }}">
                <x-auth.login-form/>
            </x-modals.regular-modal>
        </x-modals.modal-wrapper>
    </div>
@endguest
</body>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>

</html>
