<div class="form-frame bg-light rounded-5">
    <div class="form-column">
        <h3>{{ $title }}</h3>

        <div class="form-container container-fluid">
            @foreach ($computers as $key => $computer)
                <div class="card mb-2 px-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-10">
                                <h4 class="card-title">#{{ $key + 1 }} {{ $computer->serial_number }}</h4>
                                <div class="row fw-bold">{{$computer->manufacturer}} - {{$computer->type}}</div>
                            </div>
                            <div class="col-2">
                                <img src="{{ Storage::url('images/' . $computer->latestInfo()->pivot->imagename_hash) }}"
                                    alt="{{ $computer->latestInfo()->pivot->imagename }}" class="img-fluid">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <div class="row me-1">
                                    <a href="{{ route('computer.show', $computer->id) }}"
                                        class="btn btn-info">Részletek</a>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="row me-1">
                                    <a href="{{ route('computer.edit', $computer->id) }}"
                                        class="btn btn-success">Szerkesztés</a>
                                </div>
                            </div>
                            <div class="col-2">
                                <form action="{{ route('computer.destroy', $computer->id) }}" method="post">
                                    <div class="row ms-1">
                                        @csrf
                                        @method('delete')
                                        <input type="submit" value="Törlés" class="btn btn-danger">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>


@push('css')
    <link rel="stylesheet" href="{{ asset('css/wideform.css') }}">
@endpush
