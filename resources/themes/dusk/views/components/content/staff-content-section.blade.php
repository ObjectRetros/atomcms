 @props(['badge' => '', 'color' => '#327fa8'])

 <div class="w-full flex flex-col gap-y-4 rounded-lg overflow-hidden bg-[#2b303c] pb-4 shadow-sm text-gray-100">
     <div class="flex gap-x-2 bg-[#21242e] p-3">
         <div class="max-w-12.5 max-h-12.5 min-w-12.5 min-h-12.5 rounded-full relative flex items-center justify-center"
             style="background-color: {{ $color }}">
             <img src="{{ asset(sprintf('%s/%s.gif', setting('badges_path'), $badge)) }}" alt="">
         </div>

         <div class="flex flex-col justify-center text-sm">
             <p class="font-semibold text-gray-300">{{ $title }}</p>

             @if(isset($underTitle))
                 <p class="text-gray-500">{{ $underTitle }}</p>
             @endif
         </div>
     </div>

     <section class="px-3">
         {{ $slot }}
     </section>
 </div>
