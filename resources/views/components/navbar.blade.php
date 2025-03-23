<nav class="navbar navbar-expand">
    <ul class="navbar-nav">
        @foreach ($navUrls as $item)
            <li class="nav-item"><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
        @endforeach
    </ul>

    @isset($userUrls)
        <ul class="navbar-nav ms-auto pe-3">
            @stack('nav-search')

            @foreach ($userUrls as $item)
                <li class="nav-item"><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
            @endforeach
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-success">Kijelentkezés</button>
                </form>
            </li>
        </ul>
    @endisset


</nav>
