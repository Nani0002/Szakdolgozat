<nav class="navbar navbar-expand">
    <ul class="navbar-nav">
        @foreach ($navUrls as $item)
            <li class="nav-item"><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
        @endforeach
    </ul>

    @isset($userUrls)
        <ul class="navbar-nav ms-auto pe-3">
            @foreach ($userUrls as $item)
                <li class="nav-item"><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
            @endforeach
        </ul>
    @endisset

</nav>
