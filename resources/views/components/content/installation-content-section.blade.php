 @props(['icon' => '', 'classes' => ''])

 <div
     class="w-full flex flex-col gap-y-4 rounded overflow-hidden bg-white pb-3 shadow-sm {{ $classes }}">
     <div class="flex gap-x-2 border-b bg-gray-50 p-3">
         <div
             class="max-w-[50px] max-h-[50px] min-w-[50px] min-h-[50px] rounded-full relative flex items-center justify-center {{ $icon }}">
         </div>

         <div class="flex flex-col justify-center text-sm">
             <p class="font-semibold text-black">{{ $title }}</p>

             @if(isset($underTitle))
                 <p>{{ $underTitle }}</p>
             @endif
         </div>
     </div>

     <section class="px-3 text-[14px]">
         {{ $slot }}
     </section>
 </div>
