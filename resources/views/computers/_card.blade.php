<div class="col">
    <div class="card h-100 d-flex flex-column">
        @isset($computer->pivot->imagename)
            <img src="{{ Storage::url('images/' . $computer->pivot->imagename_hash) }}" class="card-img-top"
                alt="{{ $computer->pivot->imagename }}">
        @endisset
        <div class="card-body d-flex flex-column">
            <h5 class="card-title">Számítógép {{ $key + 1 }}:</h5>
            <div class="card-text row">
                <div class="col-6 fw-bold">Gyártó:</div>
                <div class="col-6">{{ $computer->manufacturer }}</div>
            </div>
            <div class="card-text row">
                <div class="col-6 fw-bold">Típus:</div>
                <div class="col-6">{{ $computer->type }}</div>
            </div>
            <div class="card-text row">
                <div class="col-6 fw-bold">Sorozatzám:</div>
                <div class="col-6">{{ $computer->serial_number }}</div>
            </div>
            <div class="card-text row">
                <div class="col-6 fw-bold">Állapot:</div>
                <div class="col-6">{{ $computer->pivot->condition }}</div>
            </div>
            <div class="card-text row">
                <div class="col-6 fw-bold">Jelszó:</div>
                <div class="col-6">{{ $computer->pivot->password }}</div>
            </div>
            <h5 class="card-title mt-2">Beszerelt alkatrészek:</h5>
            <div class="list-group">
                @foreach ($computer->extras()->wherePivot('worksheet_id', $worksheet->id)->get() as $extra)
                    <a href="{{ route('extra.edit', ['extra' => $extra->id, 'worksheet' => $worksheet->id, 'computer' => $computer->id]) }}"
                        class="list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col-6">{{ $extra->manufacturer }}</div>
                            <div class="col-6">{{ $extra->type }}</div>
                        </div>
                        <div class="row border-top mt-1 pt-1">
                            <div class="col-8">{{ $extra->serial_number }}</div>
                            <div class="col-4">
                                <form action="{{ route('extra.destroy', $extra->id) }}"
                                    method="post">
                                    @csrf
                                    @method('delete')
                                    <input type="submit" value="✖️" class="btn btn-danger mt-3">
                                </form>
                            </div>
                        </div>
                    </a>
                @endforeach
                <a href="{{ route('extra.create', ['worksheet' => $worksheet->id, 'computer' => $computer->id]) }}"
                    class="list-group-item list-group-item-action">Hozzáadás</a>
            </div>
            <div class="mt-auto d-flex justify-content-between">
                <a href="{{ route('computer.show', $computer->id) }}" class="btn btn-info mt-3">Részletek</a>
                <a href="{{ route('computer.edit', $computer->id) }}" class="btn btn-success mt-3">Szerkesztés</a>
                <button
                    data-get-url="{{ route('computer.get', ['pivot' => $computer->pivot->id, 'computer' => $computer->id]) }}"
                    id="get-{{ $key }}" type="button" class="btn btn-success mt-3 get-btn"
                    data-bs-toggle="modal" data-bs-target="#select-modal">Frissítés</button>
                <form action="{{ route('computer.detach', [$worksheet->id, $computer->id]) }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="submit" value="Törlés" class="btn btn-danger mt-3">
                </form>
            </div>
        </div>
    </div>
</div>
