@props(['icon', 'routeGroup' => '', 'classes' => '', 'childClasses' => 'min-w-[150px]', 'uppercase' => false])

<div
    x-data="{
        open: false,
        toggle() {
            if (this.open) {
                return this.close()
            }

            this.$refs.button.focus()

            this.open = true
        },
        close(focusAfter) {
            if (! this.open) return

            this.open = false

            focusAfter && focusAfter.focus()
        }
    }"
        x-on:keydown.escape.prevent.stop="close($refs.button)"
        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
        x-id="['dropdown-button']"
        @class([
            'relative md:h-[60px] text-[14px] font-semibold text-black md:border-b-4! md:border-transparent! md:hover:border-b-[#eeb425]! transition duration-200 ease-in-out dark:text-gray-200 z-5',
            $classes,
            'md:border-b-4! md:border-b-[#eeb425]!' => request()->is($routeGroup),
        ])"
>
    <!-- Button -->
    <button
        x-ref="button"
        x-on:click="toggle()"
        :aria-expanded="open"
        :aria-controls="$id('dropdown-button')"
        type="button"
        @class([
            'flex items-center md:gap-2 h-full',
            'uppercase' => $uppercase,
        ])"
    >
        @if(isset($icon))
            <i class="hidden navigation-icon {{ $icon }} lg:inline-flex"></i>
        @endif
        {{ $slot }}

        <!-- Heroicon: chevron-down -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
             fill="currentColor">
            <path fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd"/>
        </svg>
    </button>

    <!-- Panel -->
    <div
        x-ref="panel"
        x-show="open"
        x-transition.origin.top.left
        x-on:click.outside="close($refs.button)"
        :id="$id('dropdown-button')"
        style="display: none;"
         @class(['absolute left-0 mt-2 rounded bg-white dark:bg-gray-800 shadow-sm whitespace-nowrap overflow-hidden z-10', $childClasses])
    >
        {{ $children }}
    </div>
</div>
