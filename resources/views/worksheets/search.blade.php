<div class="form-frame bg-light rounded-5">
    <div class="form-column">
        <div class="d-flex w-100 justify-content-between align-items-center mb-3">
            <div class="fs-4 fw-bold">Keresés eredményei</div>
            <form class="d-flex" action="{{ route('worksheet.search') }}" method="GET">
                <input class="form-control me-2" type="search" id="search" name="id" placeholder="Keresés"
                    aria-label="Keresés" value="{{ $querry }}">
                <button class="btn btn-outline-success" type="submit">🔍</button>
            </form>
        </div>
        <div class="form-container">
            @forelse ($worksheets as $worksheet)
                <div class="card mb-2">
                    <div class="card-body">
                        <h4 class="card-title">{{ $worksheet->sheet_number }}</h4>
                        <div class="row">
                            <div class="col-3">
                                <h6 class="card-subtitle mb-2 text-body-secondary">{{ $worksheet->customer->name }}</h6>
                            </div>
                            <div class="col-6">
                                <h6 class="card-subtitle mb-2 text-body-secondary">
                                    {{ $worksheet->customer->company->name }}</h6>
                            </div>
                            <div class="col-3">
                                <h6 class="card-subtitle mb-2 text-body-secondary">{{ $worksheet->declaration_time }}
                                </h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <h6 class="card-subtitle mb-2 text-body-secondary">{{ $worksheet->customer->email }}
                                </h6>
                            </div>
                            <div class="col-6">
                                <h6 class="card-subtitle mb-2 text-body-secondary">
                                    {{ $worksheet->customer->company->email }}</h6>
                            </div>
                            @if ($worksheet->current_step == 'waiting')
                                <div class="col-3">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">Jelenleg külsősre vár!</h6>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <h6 class="card-subtitle mb-2 text-body-secondary">{{ $worksheet->customer->mobile }}
                                </h6>
                            </div>
                            <div class="col-3">
                                <h6 class="card-subtitle mb-2 text-body-secondary">
                                    {{ $worksheet->customer->company->phone }}</h6>
                            </div>
                        </div>
                        <p class="card-text">{{ $worksheet->error_description }}</p>
                        @if (!$worksheet->final)
                            <a href="{{route('worksheet.edit', $worksheet->id)}}" class="btn btn-success me-5">Szerkesztés</a>
                        @endif
                        <a href="{{ route('worksheet.show', $worksheet->id) }}" class="btn btn-info">Részletek</a>
                    </div>
                </div>
            @empty
                <p>Nincs találat.</p>
            @endforelse
        </div>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="{{ asset('css/wideform.css') }}">
@endpush
