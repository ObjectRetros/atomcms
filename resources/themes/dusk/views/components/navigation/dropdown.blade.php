@props(['icon', 'routeGroup' => '', 'classes' => '', 'childClasses' => 'min-w-[150px]', 'uppercase' => false, 'showChevron' => false, 'flexCol' => true])

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
        },
        isTouchDevice: (
            'ontouchstart' in window ||
            navigator.maxTouchPoints > 0 ||
            navigator.msMaxTouchPoints > 0
        )
    }"
    x-on:keydown.escape.prevent.stop="close($refs.button)"
    x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
    x-id="['dropdown-button']"
    x-on:mouseenter="isTouchDevice ? false : toggle()"
    x-on:mouseleave="isTouchDevice ? false : toggle()"
    x-on:click.stop="isTouchDevice ? toggle() : {}"
    @class([
        'relative h-auto font-semibold transition duration-300 ease-in-out z-5',
        'active' => request()->is($routeGroup),
        $classes,
    ])
>
    <!-- Button -->
    <button
        x-ref="button"
        :aria-expanded="open"
        :aria-controls="$id('dropdown-button')"
        type="button"
        @class([
            'flex gap-1  items-center transition ease-in-out hover:text-[#ac93da] dropdown-parent',
            'flex-col' => $flexCol,
        ])
    >
        @if(isset($icon))
            <img class="icon" src="{{ asset(sprintf('/assets/images/dusk/%s', $icon)) }}" alt="Missing icon">
        @endif

        {{ $slot }}

        @if($showChevron)
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                 fill="currentColor">
                <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"/>
            </svg>
        @endif
    </button>

    <!-- Panel -->
    <div
        x-ref="panel"
        x-show="open"
        x-transition.origin.top.left
        x-on:click.outside="close($refs.button)"
        :id="$id('dropdown-button')"
        style="display: none;"
        @class(['absolute left-0 rounded bg-[#ac93da] shadow-sm whitespace-nowrap overflow-hidden z-[100] flex flex-col py-2 items-center gap-2 dropdown-children mt-1', $childClasses])
    >
        {{ $children }}
    </div>
</div>
