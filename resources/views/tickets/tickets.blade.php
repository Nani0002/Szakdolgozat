@isset($tickets)
    <div class="ticketContainer">
        @isset($ticketTypes)
            @foreach ($ticketTypes as $ticketTypes)
            @endforeach
        @endisset
    </div>
@endisset
