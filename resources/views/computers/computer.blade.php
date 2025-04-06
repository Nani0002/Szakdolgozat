<div class="form-frame bg-light rounded-5">
    <div class="form-column">
        <div class="row">
            <div class="col-6">
                <div class="row">
                    <div class="col-12 fs-3 fw-bold">{{ $computer->serial_number }}</div>
                </div>
                <div class="row">
                    <div class="col-6 fw-bold">
                        Gyártó:
                    </div>
                    <div class="col-6 ">
                        {{ $computer->manufacturer }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 fw-bold">
                        Típus:
                    </div>
                    <div class="col-6">
                        {{ $computer->type }}
                    </div>
                </div>
                <div class="row border-top my-2">
                    <div class="col-12 fs-4 fw-bold">
                        Legutóbbi adatok:
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 fw-bold">
                        Jelszó:
                    </div>
                    <div class="col-6">
                        {{ $latest->pivot->password ?? 'Friss eszköz' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 fw-bold">
                        Állapot:
                    </div>
                    <div class="col-6">
                        {{ $latest->pivot->condition ?? 'Friss eszköz' }}
                    </div>
                </div>
                <div class="row border-top mt-2">
                    <div class="col-12 fs-5 fw-bold">
                        Helye:
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ $latest->customer->name ?? 'Friss eszköz' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ $latest->customer->mobile ?? 'Friss eszköz' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        {{ $latest->customer->email ?? 'Friss eszköz' }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                @php
                    $url = isset($latest->pivot) ? $latest->pivot->imagename_hash : 'default_computer.jpg';
                @endphp
                <img src="{{ Storage::url('images/' . $url) }}"
                    alt="{{ $latest->pivot->imagename ?? 'default_computer.jpg' }}" class="img-fluid img-thumbnail">
                <div class="d-flex">
                    <a href="{{ route('computer.edit', $computer->id) }}" class="btn btn-success mt-3 ms-auto">📝</a>
                    <form action="{{ route('computer.destroy', $computer->id) }}" method="post">
                        @csrf
                        @method('delete')
                        <input type="submit" class="btn btn-danger edit-customer-btn mt-3 ms-4" value="✖️">
                    </form>
                </div>
            </div>
        </div>
        <div class="form-container container-fluid">
            @foreach ($computer->worksheets as $worksheet)
                <div class="row border rounded-2 my-1 py-1">
                    <div class="row">
                        <div class="col-4 fw-bold">{{ $worksheet->sheet_number }}</div>
                        <div class="col-5 fw-bold">{{ $worksheet->declaration_time }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-3 d-flex flex-column"><a href="{{ route('worksheet.show', $worksheet->id) }}"
                                class="btn btn-info mt-auto">Részletek</a></div>
                        @php
                            $extras = $computer->extrasForWorksheet($worksheet->id);
                        @endphp

                        @if ($extras->isNotEmpty())
                            <div class="col-9">
                                <div class="row">
                                    <div class="col-12 fw-bold">Beszerelt extrák:</div>
                                </div>
                                @foreach ($extras as $extra)
                                    <div class="row border-top">
                                        <div class="col-4 fw-bold">Sorozatszám:</div>
                                        <div class="col-8">{{ $extra->serial_number }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 fw-bold">Gyártó:</div>
                                        <div class="col-8">{{ $extra->manufacturer }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 fw-bold">Típus:</div>
                                        <div class="col-6">{{ $extra->type }}</div>
                                        <div class="col-2">
                                            <form action="{{ route('extra.destroy', $extra->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <input type="submit" value="✖️" class="btn btn-danger mt-2 mb-1">
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="{{ asset('css/thinform.css') }}">
@endpush
