<x-app-layout>
    @push('title', __('Welcome to the best hotel on the web!'))


    <div class="col-span-12 md:col-span-6 bg-gray-900/50 rounded-xl flex flex-col py-6 px-8 text-white self-start">
        <h2 class="text-2xl">{{ __('Create a new account') }}</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf

           <div class="grid grid-cols-12 gap-3 mt-4">
               <div class="relative w-full overflow-hidden text-black col-span-12">
                   <input id="username-input" type="text" placeholder="Enter your username" name="username" value="{{ old('username') }}" class="relative py-2 rounded-md w-full" required>
               </div>

               <div class="relative w-full overflow-hidden text-black col-span-12">
                   <input id="username-input" type="email" placeholder="Enter your e-mail" name="mail" value="{{ old('mail') }}" class="relative py-2 rounded-md w-full" required>
               </div>

               <div class="col-span-12">
                   <input type="password" placeholder="Enter your password" name="password" class="relative py-2 rounded-md text-black w-full" required>
               </div>

               <div class="col-span-12">
                   <input type="password" placeholder="Confirm your password" name="password_confirmation" class="relative py-2 rounded-md text-black w-full" required>
               </div>

               @if (setting('requires_beta_code'))
                   <div class="col-span-12">
                       <input type="text" placeholder="Beta code" name="beta_code" class="relative py-2 rounded-md text-black w-full" required>
                   </div>
               @endif
           </div>

            <div class="flex items-center gap-x-3 mt-2">
                <input id="terms" type="checkbox" name="terms"
                       class="mt-1 rounded ring-0 focus:ring-0">

                <a href="{{ route('help-center.rules.index') }}" target="_blank"
                   class="mt-1 text-sm font-semibold text-white hover:text-gray-900 hover:underline hover:text-gray-200">
                    {{ __('I accept the :hotel terms & rules.', ['hotel' => setting('hotel_name')]) }}
                </a>
            </div>

            {{-- Used to determine the refer --}}
            <input type="hidden" name="referral_code" value="{{ $referral_code }}">

           <x-site-captchas />

            <div class="mt-4 grid grid-cols-2 gap-3">
                <button type="submit" class="py-2 px-4 text-white bg-yellow-500 border-2 border-yellow-300 w-full rounded-md transition duration-300 ease-in-out hover:scale-[102%]">Register</button>

                <a href="{{ route('login') }}" class="w-full">
                    <button type="button" class="py-2 px-4 text-white bg-gray-700 border-2 border-gray-600 w-full rounded-md transition duration-300 ease-in-out hover:scale-[102%]">Back to login</button>
                </a>
            </div>
        </form>
    </div>

    {{-- Articles --}}
    <div class="col-span-12 md:col-span-6 ">
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
                    <div class="swiper-slide relative article-image" style="background-image: url({{ $article->image }})">
                        <div class="absolute h-[90px] w-full left-0 bottom-0 bg-[#171a23]/95 text-white py-2 px-4">
                            <h2 class="text-3xl font-bold">
                                {{ $article->title }}
                            </h2>

                            <div class="flex justify-between items-center">
                                <div class="py-1 px-2 rounded-md bg-black/60 text-sm mt-2 flex gap-1 items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>

                                    {{ $article->user->username }}
                                </div>

                                <a href="{{ route('article.show', $article->slug) }}" class="text-sm read-more-link hover:underline">
                                    Read more
                                </a>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-span-12 grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
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
                const response = await fetch(`/api/user/${username}`);
                if (!response.ok) {
                    console.error('Failed to fetch avatar');
                    return;
                }

                const data = await response.json();


                if (!data.data.look) {
                    avatar.src = "/assets/images/dusk/ghost.png";

                    return;
                }

                avatar.src = '{{ setting('avatar_imager') }}' + '/' + data.data.look + '&direction=4&action=wav&head_direction=3';
            } catch (error) {
                console.error('An error occurred:', error);
            }
        }, 200);

        usernameInput.addEventListener('keyup', updateAvatar);

    </script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
</x-app-layout>
