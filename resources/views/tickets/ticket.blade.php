<div class="form-frame bg-light rounded-5">
    <div class="form-column">
        <div class="fs-4 mb-1 fw-bold">{{ $title }}</div>
        <div class="form-container  d-flex flex-column h-100 mb-5">
            <form action="{{ isset($ticket) ? route('ticket.update', $ticket->id) : route('ticket.store') }}"
                method="post" id="ticket-form">
                @csrf
                @isset($ticket)
                    @method('put')
                    <input type="hidden" name="id">
                @endisset
                <div class="container">
                    <div class="form-floating mb-3">
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                            type="text" placeholder="Cím" value="{{ old('title', $ticket->title ?? '') }}" />
                        <label for="title">Cím</label>
                        @error('title')
                            <div class="invalid-feedback">
                                Cím megadása kötelező
                            </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-floating mb-3">
                                <textarea class="form-control h-100 @error('text') is-invalid @enderror" id="text" name="text"
                                    placeholder="Szöveg" rows="20">{{ old('text', $ticket->text ?? '') }}</textarea>
                                <label for="text">Szöveg</label>
                                @error('text')
                                    <div class="invalid-feedback d-block">Szöveg megadása kötelező</div>
                                @enderror
                            </div>

                        </div>
                        <div class="col-6">
                            @php
                                $selected_status = old('status', $ticket->status ?? $status);
                            @endphp

                            <div class="form-floating mb-3">
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status">
                                    @foreach ($ticketTypes as $ticketType => $ticketTypePreview)
                                        <option value="{{ $ticketType }}"
                                            {{ $selected_status === $ticketType ? 'selected' : '' }}>
                                            {{ $ticketTypePreview['text'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="status">Státusz</label>
                                @error('status')
                                    <div class="invalid-feedback d-block">Nem megfelelő státusz</div>
                                @enderror
                            </div>
                            <div class="input-group mb-3">
                                @php
                                    $selected_users = old(
                                        'users',
                                        isset($ticket) ? $ticket->users->pluck('id')->toArray() : [],
                                    );
                                @endphp

                                <select class="form-multi-select @error('users') is-invalid @enderror"
                                    data-placeholder="Csatolt munkatársak" id="users" name="users[]" multiple>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ in_array($user->id, $selected_users) ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('users')
                                    <div class="invalid-feedback d-block">Legalább egy munkatárs megadása kötelező</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            @isset($ticket)
                <div class="fs-5 fw-bold">Kommentek:</div>
                <div class="container mt-3">
                    @foreach ($ticket->comments as $comment)
                        <div class="row pt-2">
                            <div class="col-1 fw-bold"><img
                                    src="{{ Storage::url('images/' . $comment->user->imagename_hash) }}"
                                    alt="{{ $comment->user->imagename }}" class="img-fluid rounded w-50"></div>
                            <div class="col-2 fw-bold d-flex align-items-center">{{ $comment->user->name }}</div>
                            <div class="col-3 d-flex align-items-center">{{ $comment->date() }}</div>
                        </div>
                        @if ($comment->user_id == $user_id)
                            <div class="row border-bottom pb-2">
                                <div class="col-10 offset-1">
                                    <form
                                        action="{{ route('comment.edit', ['comment' => $comment->id, 'ticket' => $ticket->id]) }}"
                                        method="post" id="edit-comment-{{ $comment->id }}-form">
                                        @csrf
                                        @method('put')
                                        <textarea class="form-control" id="content" name="content" type="text">{{ $comment->content }}</textarea>
                                    </form>
                                </div>
                                <div class="col-1">
                                    <div class="row">
                                        <button class="btn btn-info edit-comment-btn"
                                            id="edit-comment-{{ $comment->id }}-btn">📝</button>
                                    </div>
                                    <form action="{{ route('comment.delete', $comment->id) }}" method="post">
                                        <div class="row mt-1">
                                            @method('delete')
                                            @csrf
                                            <input type="submit" value="✖️" class="btn btn-danger">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="row border-bottom pb-2">
                                <div class="col-11 offset-1">{{ $comment->content }}</div>
                            </div>
                        @endif
                    @endforeach
                    <form action="{{ route('comment.create', ['ticket' => $ticket->id]) }}" method="post">
                        <div class="row pt-2">
                            @csrf
                            <div class="col-11">
                                <div class="mb-3">
                                    <textarea class="form-control" id="content" name="content" type="text"></textarea>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="row">
                                    <input type="submit" value="➕" class="btn btn-info">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endisset
            <div class="container mt-auto">
                <div class="row">
                    <button type="button" class="btn btn-success" id="form-submit-btn">
                        {{ isset($ticket) ? 'Frissítés' : 'Létrehozás' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="{{ asset('css/wideform.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/ticket.js') }}"></script>
@endpush
