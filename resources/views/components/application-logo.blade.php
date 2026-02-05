<a href="{{ url('/') }}">
    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <span class="sr-only">Laravel</span>
    </x-responsive-nav-link>
</a>
