 @props(['badge' => '', 'color' => '#327fa8'])

 <div class="flex w-full flex-col gap-y-4 overflow-hidden rounded bg-white pb-3 shadow-sm dark:bg-gray-800">
     <div class="flex gap-x-2 border-b bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900">
         <div class="max-w-[50px] max-h-[50px] min-w-[50px] min-h-[50px] rounded-full relative flex items-center justify-center"
             style="background-color: {{ $color }}">
             <img src="{{ asset(sprintf('%s/%s.gif', setting('badges_path'), $badge)) }}" alt="">
         </div>

         <div class="flex flex-col justify-center text-sm">
             <p class="font-semibold text-black dark:text-gray-300">{{ $title }}</p>

             @if(isset($underTitle))
                 <p class="dark:text-gray-500">{{ $underTitle }}</p>
             @endif
         </div>
     </div>

     <section class="px-3">
         {{ $slot }}
     </section>
 </div>
