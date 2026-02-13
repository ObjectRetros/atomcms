 @props(['badge' => '', 'color' => '#327fa8', 'count' => null])

 <div class="w-full flex flex-col gap-y-4 rounded-lg overflow-hidden bg-[#2b303c] pb-4 shadow-sm text-gray-100 border border-[#363c4b]">
     <div class="flex items-center justify-between gap-x-2 bg-[#21242e] p-3">
         <div class="flex gap-x-2">
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

         @if (!is_null($count))
             <span class="rounded-full border border-[#4a5060] bg-[#2b303c] px-2.5 py-1 text-xs font-semibold text-gray-300">
                 {{ trans_choice('{0} No staff members|{1} :count staff member|[2,*] :count staff members', $count, ['count' => $count]) }}
             </span>
         @endif
     </div>

     <section class="px-3">
         {{ $slot }}
     </section>
 </div>
