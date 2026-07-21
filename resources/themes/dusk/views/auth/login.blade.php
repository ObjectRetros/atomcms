<x-app-layout>
    @push('title', __('Welcome to the best hotel on the web!'))


    <div class="col-span-12 md:col-span-6 min-h-[250px] bg-gray-900/50 rounded-xl flex flex-col py-6 px-8 text-white">
        <h2 class="text-2xl">{{ __('Login') }}</h2>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="relative w-full overflow-hidden text-black">
                <input id="username-input" type="text" placeholder="{{ __('Enter your username') }}" name="username" class="relative py-2 rounded-md mt-3 w-full">

                <img id="user-avatar" class="absolute right-0 -top-4" src="{{ asset('/assets/images/dusk/ghost.png') }}" alt="">
            </div>

            <input type="password" placeholder="{{ __('Enter your password') }}" name="password" class="relative py-2 rounded-md mt-3 text-black w-full">

            <x-site-captchas />

            <div class="mt-4 flex gap-4">
                <button type="submit" class="py-2 px-4 text-white bg-yellow-500 border-2 border-yellow-300 w-full rounded-md transition duration-300 ease-in-out hover:scale-[102%]">{{ __('Login') }}</button>

                <a href="{{ route('register') }}" class="w-full">
                    <button type="button" class="py-2 px-4 text-white bg-gray-700 border-2 border-gray-600 w-full rounded-md transition duration-300 ease-in-out hover:scale-[102%]">{{ __('Register') }}</button>
                </a>
            </div>
        </form>
    </div>

    {{-- Articles --}}
    <div class="col-span-12 md:col-span-6 h-[250px]">
        <!-- Slider main container -->
        <div class="swiper h-[250px] rounded-md">

            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>

            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>

            <!-- Additional required wrapper -->
            <div class="swiper-wrapper" style="z-index: 14;">
                @foreach($articles as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>
        </div>

    </div>

    <div class="col-span-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($photos as $photo)
            <a href="{{ $photo->url }}" data-fancybox="gallery" class="cursor-pointer relative transition duration-300 ease-in-out hover:scale-[102%]">
                <div class="photo-overlay"></div>
                <img class="h-[250px] w-full object-cover object-center rounded-md custom-shadow" src="{{ $photo->url }}" alt="">

                <div class="absolute right-2 bottom-2 bg-black/70 p-2 rounded-md text-white flex gap-x-2 z-[5]">
                    <img class="self-center" src="{{ asset('/assets/images/dusk/author_camera_icon.png') }}" alt="">
                    <small>
                        {{ $photo->user->username }}
                    </small>
                </div>
            </a>
        @endforeach
    </div>

    <script>
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }

        const avatar = document.getElementById('user-avatar');
        const usernameInput = document.getElementById('username-input');

        const updateAvatar = debounce(async () => {
            const username = usernameInput.value;
            if (!username) return;

            try {
                const response = await fetch(`/api/user/${encodeURIComponent(username)}`);
                if (!response.ok) {
                    console.error('Failed to fetch avatar');
                    return;
                }

                const data = await response.json();


                if (!data.data.look) {
                    avatar.src = "/assets/images/dusk/ghost.png";

                    return;
                }

                avatar.src = '{{ setting('avatar_imager') }}' + data.data.look + '&direction=4&action=wav&head_direction=3';
            } catch (error) {
                console.error('An error occurred:', error);
            }
        }, 200);

        usernameInput.addEventListener('keyup', updateAvatar);

    </script>
</x-app-layout>
