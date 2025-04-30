@props(['tabs' => [], 'active' => null, 'classId' => null])

<div class="w-full overflow-x-auto hide-scrollbar">
   <div class="inline-flex gap-4 items-center px-2 whitespace-nowrap min-w-max">
      @foreach($tabs as $tab)
         <a href="{{ route('classroom.tab', ['class' => $classId, 'tab' => strtolower($tab)]) }}">
             <button
                 class="p-1 text-sm font-medium text-sky-800 rounded-2xl hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-600
                     {{ $tab === ucfirst($active) ? 'bg-sky-100' : '' }}"
             >
                 {{ $tab }}
             </button>
         </a>
      @endforeach
   </div>
</div>






