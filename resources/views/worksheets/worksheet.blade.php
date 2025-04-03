<div class="form-frame bg-light rounded-5">
    <div class="form-column">
        <div class="form-container container-fluid">
            <div class="row border-bottom pb-3">
                <div class="col-6">
                    <div class="row">
                        <div class="col-12">
                            <div class="fs-3 fw-bold">Bizonylatszám: {{ $worksheet->sheet_number }}
                                @switch($worksheet->current_step)
                                    @case('open')
                                        <h5><span class="badge rounded-pill text-bg-primary worksheet-pill">Felvéve</span></h5>
                                    @break

                                    @case('started')
                                        <h5><span class="badge rounded-pill text-bg-secondary worksheet-pill">Kiosztva</span>
                                        </h5>
                                    @break

                                    @case('ongoing')
                                        <h5><span class="badge rounded-pill text-bg-success worksheet-pill">Folyamatban</span>
                                        </h5>
                                    @break

                                    @case('price_offered')
                                        <h5><span class="badge rounded-pill text-bg-info worksheet-pill">Árajánlat kiadva</span>
                                        </h5>
                                    @break

                                    @case('waiting')
                                        <h5><span class="badge rounded-pill text-bg-dark worksheet-pill">Külsősre várunk</span>
                                        </h5>
                                    @break

                                    @case('to_invoice')
                                        <h5><span class="badge rounded-pill text-bg-warning worksheet-pill">Számlázni</span>
                                        </h5>
                                    @break

                                    @case('closed')
                                        <h5><span class="badge rounded-pill text-bg-danger worksheet-pill">Lezárva</span></h5>
                                    @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fs-5 fw-bold">
                            Munka típusa:
                        </div>
                        <div class="col-6 fs-5">
                            @switch($worksheet->sheet_type)
                                @case('paid')
                                    Fizetős
                                @break

                                @case('maintanance')
                                    Karbantartós
                                @break

                                @case('warranty')
                                    Garanciális
                                @break

                                @default
                            @endswitch
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fs-5 fw-bold">
                            Utolsó nyomtatás ideje:
                        </div>
                        <div class="col-6 fs-5">
                            {{ $worksheet->print_date }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fs-5 fw-bold">
                            Bejelentés módja:
                        </div>
                        <div class="col-6 fs-5">
                            @switch($worksheet->declaration_mode)
                                @case('email')
                                    E-mailben
                                @break

                                @case('phone')
                                    Telefonon
                                @break

                                @case('personal')
                                    Személyesen
                                @break

                                @case('onsite')
                                    Helyszíni
                                @break
                            @endswitch
                        </div>
                    </div>
                    <div class="row mt-2 pt-2 border-top">
                        <div class="col-6 fs-5 fw-bold">
                            Elvégzett feladat:
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fw-bold">
                            Kezdete:
                        </div>
                        <div class="col-6">
                            {{ $worksheet->work_start }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fw-bold">
                            Vége:
                        </div>
                        <div class="col-6">
                            {{ $worksheet->work_end }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fw-bold">
                            30 perces egység:
                        </div>
                        <div class="col-6">
                            {{ $worksheet->worktime }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fw-bold">
                            Elvégzett feladat:
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            {{ $worksheet->work_description }}
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card mb-2">
                        <div class="card-body">
                            <h4 class="card-title">{{ $worksheet->customer->name }} /
                                {{ $worksheet->customer->company->name }}</h4>
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">Felvétel dátuma:
                                        {{ $worksheet->declaration_time }}</h6>
                                </div>
                            </div>
                            <div class="row">
                                <h6 class="card-text mb-2">Elérhetőségek</h6>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">{{ $worksheet->customer->email }}
                                    </h6>
                                </div>
                                <div class="col-6">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">
                                        {{ $worksheet->customer->company->email }}</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">
                                        {{ $worksheet->customer->mobile }}
                                    </h6>
                                </div>
                                <div class="col-6">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">
                                        {{ $worksheet->customer->company->phone }}</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">
                                        {{ $worksheet->customer->company->post_code }}
                                    </h6>
                                </div>
                                <div class="col-4">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">
                                        {{ $worksheet->customer->company->city }}</h6>
                                </div>
                                <div class="col-6">
                                    <h6 class="card-subtitle mb-2 text-body-secondary">
                                        {{ $worksheet->customer->company->street }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="row">
                        <div class="col-12 fs-5 mt-2 fw-bold">
                            Belső munkaleírás:
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fw-bold">
                            Belső munkatárs:
                        </div>
                        <div class="col-6">
                            {{ $worksheet->coworker->name }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 fw-bold">
                            Belső felelős:
                        </div>
                        <div class="col-6">
                            {{ $worksheet->liable->name }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 fw-bold">
                            Hibaleírás:
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            {{ $worksheet->error_description }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 fw-bold">
                            Egyéb komment:
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            {{ $worksheet->comment }}
                        </div>
                    </div>
                </div>
                @isset($worksheet->outsourcing)
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12 fs-5 mt-2 fw-bold">
                                Külső munkaleírás:
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 fw-bold">
                                Külső munkalapszám:
                            </div>
                            <div class="col-6">
                                {{ $worksheet->outsourcing->outsourced_number }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 fw-bold">
                                Külső cég:
                            </div>
                            <div class="col-6">
                                {{ $worksheet->outsourcing->company->name }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 fw-bold">
                                Beviteli idő:
                            </div>
                            <div class="col-6">
                                {{ $worksheet->outsourcing->entry_time }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 fw-bold">
                                Külső státusz:
                            </div>
                            <div class="col-6">
                                @switch($worksheet->outsourcing->finished)
                                    @case('ongoing')
                                        Munka alatt
                                    @break

                                    @case('finished')
                                        Elkészült
                                    @break

                                    @case('brought')
                                        Elhozva
                                    @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
            <div class="row border-top my-2">
                <div class="col-12">
                    @if (isset($worksheet->computers) && count($worksheet->computers))
                        <div class="fs-5 my-2 fw-bold">
                            Beadott számítógép@php echo count($worksheet->computers) > 1 ? 'ek:' : ':' @endphp
                        </div>
                        <div class="row row-cols-3 g-4" id="computer-container">
                            @foreach ($worksheet->computers as $key => $computer)
                                @include('computers._card', ['computer' => $computer, 'key' => $key])
                            @endforeach
                            <div class="col">
                                <div class="card h-75 d-flex flex-column p-3">
                                    <a id="add-computer" href="{{ route('computer.create') }}"
                                        class="h-100 d-flex">+</a>
                                </div>
                                <div class="card h-25 d-flex flex-column p-3">
                                    <button id="select-computer" class="h-100 d-flex" data-bs-toggle="modal"
                                        data-bs-target="#select-modal"
                                        data-get-url={{ route('computer.select', $worksheet->id) }}>Kiválasztás</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row mt-3">
                <a href="{{ route('worksheet.edit', $worksheet->id) }}" class="btn btn-success">Szerkesztés</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="select-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-floating">
                    <select class="form-select" id="computer_id" name="computer_id"></select>
                    <label for="computer_id">Számítógép sorozatszám</label>
                </div>
                <div class="row mt-3">
                    <div class="col-2 fw-bold">Gyártó:</div>
                    <div class="col-10" id="static-manufacturer"></div>
                </div>
                <div class="row mt-2">
                    <div class="col-2 fw-bold">Típus:</div>
                    <div class="col-10" id="static-type"></div>
                </div>
                <div class="form-floating mt-2">
                    <input class="form-control" id="condition" name="condition" type="text"
                        placeholder="Állapot" />
                    <label for="condition">Állapot</label>
                </div>
                <div class="form-floating mt-2">
                    <input class="form-control" id="password" name="password" type="text"
                        placeholder="Jelszó" />
                    <label for="password">Jelszó</label>
                </div>
                <div class="row mt-3">
                    <div class="col-6 offset-3 position-relative">
                        <img src="" alt="Preview" class="img-fluid" id="prewiew">
                        <input type="file" id="imagefile" class="d-none" accept="image/*">
                        <button class="btn btn-light rounded-circle plus-btn border-dark border-2"
                            id="changeImageBtn"><b>+</b></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="attach-btn"
                    data-attach-url="{{ route('computer.attach', $worksheet->id) }}"
                    data-refresh-url="{{ route('computer.refresh') }}"
                    data-csrf-token="{{ csrf_token() }}">Kiválasztás</button>
            </div>
        </div>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="{{ asset('css/wideform.css') }}">
    <link rel="stylesheet" href="{{ asset('css/worksheet.css') }}">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/computer.js') }}"></script>
@endpush
