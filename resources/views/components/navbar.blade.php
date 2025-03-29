<nav class="navbar navbar-expand">
    <ul class="navbar-nav">
        @foreach ($navUrls as $item)
            <li class="nav-item"><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
        @endforeach
    </ul>

    @isset($userUrls)
        <ul class="navbar-nav ms-auto pe-3">
            @if (isset($searchbar) && $searchbar)
                <form class="d-flex" action="{{ route('worksheet.search') }}" method="GET">
                    <input class="form-control me-2" type="search" id="search" name="id" placeholder="Keresés"
                        aria-label="Keresés">
                    <button class="btn btn-outline-success" type="submit">🔍</button>
                </form>
            @endisset

            @foreach ($userUrls as $item)
                @if ($item['name'] == 'search')
                    <form class="d-flex" action="{{ $item['url'] }}" method="GET">
                        <input class="form-control me-2" type="search" id="search" name="id"
                            placeholder="Keresés" aria-label="Keresés">
                        <button class="btn btn-outline-success" type="submit">🔍</button>
                    </form>
                @else
                    <li class="nav-item"><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                @endif
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
