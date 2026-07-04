@props([
    'title',
    'icon',
    'data',
    'valueType',
    'formatValue' => null,
])

<div class="flex flex-col gap-y-3">
    <div class="flex gap-2 rounded-md py-2 px-4 bg-[#21242e]/90 text-gray-100 font-bold">
        <div class="flex items-center">
            <img src="{{ asset('/assets/images/icons/' . $icon) }}" alt="" class="w-4" style="image-rendering: pixelated;">
        </div>

        {{ $valueType }}
    </div>

    @foreach ($data as $index => $entry)
        <div class="p-3 rounded-md flex items-center justify-between h-[60px] overflow-hidden bg-[#21242e]/90">
           <div class="flex gap-2 items-center">
              <div class="w-12 h-12 rounded-full overflow-hidden relative leaderboard-background">
                  <img class="absolute -top-2 left-0"
                       src="{{ setting('avatar_imager') }}{{ $entry->user->look }}&head_direction=3&gesture=sml"
                       alt=""/>
              </div>

               <div class="flex flex-col">
                   <p class="font-bold text-gray-100">
                       {{ $entry->user->username }}
                   </p>
                   <p class="text-gray-200 text-sm">
                       {{ $formatValue ? $formatValue($entry->value) : $entry->value }} {{ $valueType }}
                   </p>
               </div>
           </div>

            <div @class([
                    'flex items-center justify-center',
                    'w-8 h-8 rounded-full bg-gray-300' => ($index + 1) > 3,
                    'leaderboard-position first' => ($index + 1) == 1,
                    'leaderboard-position second' => ($index + 1) == 2,
                    'leaderboard-position third' => ($index + 1) == 3,
                ])>
                @if(($index + 1) > 3)
                    {{ ($index + 1) }}
                @endif
            </div>
        </div>
    @endforeach
</div>
