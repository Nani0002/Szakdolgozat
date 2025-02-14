@isset($tickets)
    <div class="ticket-frame bg-light rounded-5">
        @isset($ticketTypes)
            @foreach ($ticketTypes as $ticketType)
                <div class="ticket-column" id="ticket-column-{{$ticketType}}">
                    @switch($ticketType)
                        @case('open')
                            <h5><span class="badge rounded-pill text-bg-primary ticket-pill">Felvéve</span></h5>
                        @break

                        @case('started')
                            <h5><span class="badge rounded-pill text-bg-secondary ticket-pill">Kiosztva</span></h5>
                        @break

                        @case('ongoing')
                            <h5><span class="badge rounded-pill text-bg-success ticket-pill">Folyamatban</span></h5>
                        @break

                        @case('price_offered')
                            <h5><span class="badge rounded-pill text-bg-info ticket-pill">Árajánlat kiadva</span></h5>
                        @break

                        @case('waiting')
                            <h5><span class="badge rounded-pill text-bg-dark ticket-pill">Külsősre várunk</span></h5>
                        @break

                        @case('to_invoice')
                            <h5><span class="badge rounded-pill text-bg-warning ticket-pill">Számlázni</span></h5>
                        @break

                        @case('closed')
                            <h5><span class="badge rounded-pill text-bg-danger ticket-pill">Lezárva</span></h5>
                        @break
                    @endswitch
                    <div class="ticket-container accordion" id="ticket-container-{{$ticketType}}">
                        @foreach ($tickets[$ticketType] as $ticket)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{$ticket->id}}" aria-expanded="true" aria-controls="collapse{{$ticket->id}}">
                                        {{$ticket->title}}
                                    </button>
                                </h2>
                                <div id="collapse{{$ticket->id}}" class="accordion-collapse collapse"
                                    data-bs-parent="#ticket-container-{{$ticketType}}">
                                    <div class="accordion-body">
                                        {{$ticket->text}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endisset
    </div>
@endisset

@push('css')
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
@endpush
