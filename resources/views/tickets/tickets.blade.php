@isset($tickets)
    <div class="ticket-frame bg-light rounded-5" data-update-url="{{ route('ticket.move') }}"
        data-csrf-token="{{ csrf_token() }}" id="dragdrop-frame">
        @isset($ticketTypes)
            @foreach ($ticketTypes as $ticketType => $ticketTypePreview)
                <div class="ticket-column" id="ticket-column-{{ $ticketType }}">
                    <h5><span class="badge rounded-pill text-bg-{{$ticketTypePreview["color"]}} ticket-pill">{{$ticketTypePreview["text"]}}</span></h5>
                    <div class="ticket-container dragdrop-container accordion" id="ticket-container-{{ $ticketType }}"
                        ondrop="drop(event)" ondragover="allowDrop(event)">
                        @foreach ($tickets[$ticketType] as $ticket)
                            <div class="accordion-item" draggable="true" ondragstart="drag(event)"
                                id="accordion-{{ $ticket->id }}" data-slot="{{ $ticket->slot_number }}"
                                data-delete-url={{ route('ticket.destroy', ['ticket' => $ticket->id]) }}
                                data-close-url={{ route('ticket.close', ['ticket' => $ticket->id]) }}>
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $ticket->id }}" aria-expanded="true"
                                        aria-controls="collapse{{ $ticket->id }}">
                                        {{ $ticket->title }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $ticket->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#ticket-container-{{ $ticketType }}">
                                    <div class="accordion-body">
                                        {{ $ticket->text }}
                                        <div class="row mt-3">
                                            <div class="col-5">
                                                <a href="{{ route('ticket.show', $ticket->id) }}"
                                                    class="btn btn-info">Részletek</a>
                                            </div>
                                            <div class="col-6">
                                                @if ($ticketType != 'closed')
                                                    <form action="{{ route('ticket.close', ['ticket' => $ticket->id]) }}"
                                                        method="POST" id="close-form-{{ $ticket->id }}">
                                                        @csrf
                                                        @method('patch')
                                                        <input type="submit" value="Lezárás" class="btn btn-danger ms-3 mb-3"
                                                            id="close-btn-{{ $ticket->id }}">
                                                    </form>
                                                @else
                                                    <form action="{{ route('ticket.destroy', ['ticket' => $ticket->id]) }}"
                                                        method="POST" id="close-form-{{ $ticket->id }}">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="submit" value="Törlés" class="btn btn-danger ms-3 mb-3"
                                                            id="close-btn-{{ $ticket->id }}">
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="accordion-item p-3">
                            <a href="{{route('ticket.create', ["status" => $ticketType])}}" draggable="false" class="d-flex plus-btn">+</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endisset
    </div>
@endisset

@push('css')
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/dragdrop.js') }}"></script>
@endpush
