<nav class="my-6 pl-4 pr-4 flex gap-4 items-center justify-between">
    <div class="flex gap-4">
        <x-nav-link href="{{ route('about') }}" :active="Route::is('about')">About</x-nav-link>
        {{--        <x-nav-link href="{{ route('cdramas.index') }}" :active="Route::is('cdramas.index')">Cdramas</x-nav-link>--}}
        {{--        <x-nav-link href="{{ route('contact') }}" :active="Route::is('contact')">Contact</x-nav-link>--}}
    </div>

    {{-- Right-side auth section --}}
    <div class="flex gap-4 items-center">
        @auth
            {{-- Optional: show user's name --}}
            <span
                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700">
                Hello, {{ Auth::user()->name }}!
            </span>

            {{-- Optional: link to profile --}}
            <x-nav-link href="{{ route('profile.edit') }}" :active="Route::is('profile.edit')">Profile</x-nav-link>

            {{-- Admin button --}}
            @if(Auth::user()->role->name === 'Admin')
                <x-nav-link href="{{ route('admin.cdramas.index') }}" :active="Route::is('admin.cdramas.index')">
                    Admin Overview
                </x-nav-link>
            @endif

            {{-- Logout button --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                    Logout
                </button>
            </form>
        @endauth

        {{-- If user is NOT logged in --}}
        @guest
            <x-nav-link href="{{ route('login') }}" :active="Route::is('login')">Login</x-nav-link>
            <x-nav-link href="{{ route('register') }}" :active="Route::is('register')">Register</x-nav-link>
        @endguest
    </div>
</nav>
