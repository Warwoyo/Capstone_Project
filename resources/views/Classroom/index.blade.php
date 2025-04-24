@extends('layouts.dashboard')

@section('content')

<main class="flex mx-auto w-full max-w-full h-screen bg-white">

    <!-- Main Content -->
    <section class="flex-1 pt-10 px-8 max-md:p-4 max-sm:p-2.5">

        <header class="flex flex-col gap-4">
            <nav class="text-sm text-slate-600">
                <span class="text-sky-600">Manajemen Kelas &gt;</span>
                <span class="text-slate-600">Daftar Kelas</span>
            </nav>
            <h1 class="text-lg font-bold text-sky-800">Daftar Kelas</h1>
            <div class="flex gap-5 items-center flex-nowrap w-full">
  <!-- Search Bar -->
  <div class="flex items-center px-4 py-0 bg-white rounded-3xl border border-sky-600 h-[39px] flex-grow">
    <div>
      <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.66668 14.4999C11.1645 14.4999 14 11.6644 14 8.16659C14 4.66878 11.1645 1.83325 7.66668 1.83325C4.16887 1.83325 1.33334 4.66878 1.33334 8.16659C1.33334 11.6644 4.16887 14.4999 7.66668 14.4999Z" stroke="#0086C9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M14.6667 15.1666L13.3333 13.8333" stroke="#0086C9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>
    </div>
    <input type="text" placeholder="Cari kelas...." class="text-base text-sky-600 bg-transparent border-none outline-none ml-2 w-full">
  </div>

  <!-- Add Class Button -->
  <button class="flex justify-center items-center bg-sky-600 rounded-3xl border border-sky-600 h-[38px] px-6 whitespace-nowrap">
    <svg width="168" height="38" viewBox="0 0 168 38" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M23.6333 14.3462V23.6539" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
      <path d="M18.1333 19H29.1333" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
      <text fill="white" xml:space="preserve" style="white-space: pre" font-family="Poppins" font-size="14" letter-spacing="0em">
        <tspan x="44.4575" y="23.9">Tambah Kelas</tspan>
      </text>
    </svg>
  </button>
</div>

        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 pt-6 max-w-screen-xl mx-auto">
    @foreach ($classroom as $class)
    <div class="p-4 border rounded shadow-lg">
        <h2 class="text-xl font-bold text-sky-800">{{ $class['title'] }}</h2>
        <p class="text-sm text-slate-600">{{ $class['description'] }}</p>
    </div>
    @endforeach
</div>

    </section>

    <!-- Header Icons -->
    <div class="absolute top-5 right-5">
    <button>
      <!-- Notification Icon -->
      <svg width="30" height="31" viewBox="0 0 30 31" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M15.025 4.13745C10.8875 4.13745 7.52499 7.49995 7.52499 11.6375V15.25C7.52499 16.0125 7.19999 17.1749 6.81249 17.8249L5.37499 20.2125C4.48749 21.6875 5.09999 23.325 6.72499 23.875C12.1125 25.675 17.925 25.675 23.3125 23.875C24.825 23.375 25.4875 21.5875 24.6625 20.2125L23.225 17.8249C22.85 17.1749 22.525 16.0125 22.525 15.25V11.6375C22.525 7.51245 19.15 4.13745 15.025 4.13745Z" stroke="#0086C9" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round"></path>
        <path d="M17.3375 4.50005C16.95 4.38755 16.55 4.30005 16.1375 4.25005C14.9375 4.10005 13.7875 4.18755 12.7125 4.50005C13.075 3.57505 13.975 2.92505 15.025 2.92505C16.075 2.92505 16.975 3.57505 17.3375 4.50005Z" stroke="#0086C9" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M18.775 24.325C18.775 26.3875 17.0875 28.075 15.025 28.075C14 28.075 13.05 27.65 12.375 26.975C11.7 26.3 11.275 25.35 11.275 24.325" stroke="#0086C9" stroke-width="2" stroke-miterlimit="10"></path>
      </svg>
    </button>
    <button>
      <!-- Profile Icon -->
      <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M17.675 18.6375C17.5729 18.6229 17.4417 18.6229 17.325 18.6375C14.7583 18.55 12.7167 16.45 12.7167 13.8688C12.7167 11.2292 14.8458 9.08545 17.5 9.08545C20.1396 9.08545 22.2833 11.2292 22.2833 13.8688C22.2688 16.45 20.2417 18.55 17.675 18.6375Z" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M27.3292 28.2624C24.7333 30.6395 21.2917 32.0832 17.5 32.0832C13.7083 32.0832 10.2667 30.6395 7.67084 28.2624C7.81667 26.8916 8.69167 25.5499 10.2521 24.4999C14.2479 21.8457 20.7813 21.8457 24.7479 24.4999C26.3083 25.5499 27.1833 26.8916 27.3292 28.2624Z" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        <path d="M17.5 32.0834C25.5542 32.0834 32.0834 25.5542 32.0834 17.5001C32.0834 9.44593 25.5542 2.91675 17.5 2.91675C9.44587 2.91675 2.91669 9.44593 2.91669 17.5001C2.91669 25.5542 9.44587 32.0834 17.5 32.0834Z" stroke="#0086C9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>
    </button>
    </div>
</main>


@endsection
