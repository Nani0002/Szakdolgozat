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
                        <div class="row row-cols-3 g-4">
                            @foreach ($worksheet->computers as $key => $computer)
                                <div class="col">
                                    <div class="card h-100 d-flex flex-column">
                                        @isset($computer->pivot->imagename)
                                            <img src="{{ Storage::url('images/' . $computer->pivot->imagename_hash) }}"
                                                class="card-img-top" alt="{{ $computer->pivot->imagename }}">
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
                                            <div class="mt-auto"><a
                                                    href="{{ route('computer.show', $computer->id) }}"
                                                    class="btn btn-info mt-3">Részletek</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <a href="{{route('worksheet.edit', $worksheet->id)}}" class="btn btn-success">Szerkesztés</a>
            </div>
        </div>
    </div>
</div>


@push('css')
    <link rel="stylesheet" href="{{ asset('css/wideform.css') }}">
@endpush
