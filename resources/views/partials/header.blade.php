<header class="d-flex justify-content-center py-3">
    <ul class="nav nav-pills">
        @guest
            <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">Login</a></li>
            <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">Register</a></li>
        @endguest

        @auth
            <li class="nav-item"><a href="{{ route('home') }}" class="nav-link">Dashboard</a></li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="#" class="nav-link"
                        onclick="event.preventDefault();
                        this.closest('form').submit();">
                        Logout
                    </a>
                </form>
            </li>
        @endauth
    </ul>
</header>
