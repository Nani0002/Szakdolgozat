<nav>
    <ul>
        @foreach($urls as $item)
            <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
        @endforeach
    </ul>
</nav>
