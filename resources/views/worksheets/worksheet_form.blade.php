<div class="form-frame bg-light rounded-5">
    <div class="form-column">
        <div class="fs-2 mb-1 fw-bold">{{ isset($worksheet) ? 'Munkalap szerkesztése' : 'Munkalap létrehozása' }}</div>
        <div class="form-container page container">
            <form action="{{ isset($worksheet) ? route('worksheet.update', $worksheet->id) : route('worksheet.store') }}"
                method="post" class="container" data-user-id="{{ $loggedIn }}" id="form">
                @csrf
                @isset($worksheet)
                    @method('put')
                @endisset
                <div class="row">
                    <div class="col-6">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="sheet_number" name="sheet_number" type="text"
                                placeholder="Munkalapszám" value="{{ old('sheet_number', $worksheet->sheet_number ?? '') }}" @disabled(isset($worksheet))/>
                            <label for="sheet_number">Munkalapszám</label>
                        </div>
                        <div class="input-group mb-3">
                            @php
                                $selected_sheet_type = old('sheet_type', $worksheet->sheet_type ?? '');
                            @endphp

                            <div class="form-floating">
                                <select class="form-select" id="sheet_type" name="sheet_type">
                                    <option value="maintanance"
                                        {{ $selected_sheet_type === 'maintanance' ? 'selected' : '' }}>
                                        Karbantartós</option>
                                    <option value="paid" {{ $selected_sheet_type === 'paid' ? 'selected' : '' }}>
                                        Fizetős</option>
                                    <option value="warranty"
                                        {{ $selected_sheet_type === 'warranty' ? 'selected' : '' }}>Személyesen
                                    </option>Garanciális
                                </select>
                                <label for="declaration_mode">Munkalap típusa</label>
                            </div>
                            @php
                                $selected_current_step = old('current_step', $worksheet->current_step ?? $current_step);
                            @endphp

                            <div class="form-floating">
                                <select class="form-select" id="current_step" name="current_step">
                                    @foreach ($worksheetTypes as $worksheetType)
                                        <option value="{{ $worksheetType }}"
                                            {{ $selected_current_step === $worksheetType ? 'selected' : '' }}>
                                            @switch($worksheetType)
                                                @case('open')
                                                    Felvéve
                                                @break

                                                @case('started')
                                                    Kiosztva
                                                @break

                                                @case('ongoing')
                                                    Folyamatban
                                                @break

                                                @case('price_offered')
                                                    Árajánlat kiadva
                                                @break

                                                @case('waiting')
                                                    Külsősre várunk
                                                @break

                                                @case('to_invoice')
                                                    Számlázni
                                                @break

                                                @case('closed')
                                                    Lezárva
                                                @break
                                            @endswitch
                                        </option>
                                    @endforeach
                                </select>
                                <label for="current_step">Munkalap állapota</label>
                            </div>
                            @php
                                $selected_declaration_mode = old(
                                    'declaration_mode',
                                    $worksheet->declaration_mode ?? '',
                                );
                            @endphp

                            <div class="form-floating">
                                <select class="form-select" id="declaration_mode" name="declaration_mode">
                                    <option value="email"
                                        {{ $selected_declaration_mode === 'email' ? 'selected' : '' }}>
                                        E-mailben</option>
                                    <option value="phone"
                                        {{ $selected_declaration_mode === 'phone' ? 'selected' : '' }}>
                                        Telefonon</option>
                                    <option value="personal"
                                        {{ $selected_declaration_mode === 'personal' ? 'selected' : '' }}>Személyesen
                                    </option>
                                    <option value="onsite"
                                        {{ $selected_declaration_mode === 'onsite' ? 'selected' : '' }}>
                                        Helyszíni</option>
                                </select>
                                <label for="declaration_mode">Bejelentés módja</label>
                            </div>
                        </div>
                        @php
                            $declaration_date = old(
                                'declaration_time',
                                isset($worksheet) && $worksheet->declaration_time
                                    ? $worksheet->declaration_time->format('Y-m-d')
                                    : '',
                            );
                            $declaration_time = old(
                                'declaration_time_hour',
                                isset($worksheet) && $worksheet->declaration_time
                                    ? $worksheet->declaration_time->format('H:i')
                                    : '',
                            );
                        @endphp

                        <div class="input-group mb-3">
                            <label for="declaration_time" class="input-group-text w-30">Felvétel ideje</label>
                            <button class="btn btn-outline-secondary time-btn" type="button"
                                id="declaration_time-btn">Most</button>

                            <input id="declaration_time" class="form-control" type="date" name="declaration_time"
                                value="{{ $declaration_date }}" />
                            <input id="declaration_time_hour" class="form-control" type="time"
                                name="declaration_time_hour" value="{{ $declaration_time }}" />
                        </div>
                        <div class="form-check form-switch fs-4 mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="print_check">
                            <label class="form-check-label" for="print">Nyomtatás kérése</label>
                        </div>
                        @php
                            $print_date = old(
                                'print_date',
                                isset($worksheet) && $worksheet->print_date
                                    ? $worksheet->print_date->format('Y-m-d')
                                    : '',
                            );
                            $print_time = old(
                                'print_date_hour',
                                isset($worksheet) && $worksheet->print_date
                                    ? $worksheet->print_date->format('H:i')
                                    : '',
                            );
                        @endphp

                        <div class="input-group mb-3">
                            <div class="form-floating">
                                <input id="print_date" class="form-control" type="date" name="print_date"
                                    value="{{ $print_date }}" />
                                <label for="print_date">Nyomtatás dátuma</label>
                            </div>
                            <div class="form-floating">
                                <input id="print_date_hour" class="form-control" type="time" name="print_date_hour"
                                    value="{{ $print_time }}" />
                                <label for="print_date_hour">Nyomtatás ideje</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-3">
                            <button class="btn btn-outline-secondary time-btn" type="button"
                                id="liable-btn">Én</button>
                            <div class="form-floating">
                                @php
                                    $selected_liable_id = old('liable_id', $worksheet->liable_id ?? '');
                                @endphp

                                <select class="form-select" id="liable_id" name="liable_id">
                                    @foreach ($users as $user)
                                        @if ($user->role == 'liable')
                                            <option value="{{ $user->id }}" id="liable-id-{{ $user->id }}"
                                                {{ $selected_liable_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <label for="liable_id">Belső felelős</label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <button class="btn btn-outline-secondary time-btn" type="button"
                                id="coworker-btn">Én</button>
                            <div class="form-floating">
                                @php
                                    $selected_coworker_id = old('coworker_id', $worksheet->coworker_id ?? '');
                                @endphp

                                <select class="form-select" id="coworker_id" name="coworker_id">
                                    @foreach ($users as $user)
                                        @if ($user->role != 'admin')
                                            <option value="{{ $user->id }}" id="coworker-id-{{ $user->id }}"
                                                {{ $selected_coworker_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <label for="coworker_id">Belső munkatárs</label>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            @php
                                $selected_company_id = old('company_id', $worksheet->customer->company_id ?? 1);
                            @endphp
                            <select class="form-select" id="company_id" name="company_id" data-update-url="{{route("company.customers")}}">
                                @foreach ($companies->where('type', 'customer') as $company)
                                    <option value="{{ $company->id }}" id="company-id-{{ $company->id }}"
                                        {{ $selected_company_id == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}</option>
                                @endforeach
                            </select>
                            <label for="company_id">Ügyfél cég</label>
                        </div>
                        <div class="form-floating mb-3">
                            @php
                                $selected_customer_id = old('customer_id', $worksheet->customer_id ?? 1);
                            @endphp
                            <select class="form-select" id="customer_id" name="customer_id">
                                @foreach ($companies->where('id', $selected_company_id)->first()->customers as $customer)
                                    <option value="{{ $customer->id }}" id="customer-id-{{ $customer->id }}"
                                        {{ $selected_customer_id == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <label for="customer_id">Ügyfél munkatárs</label>
                        </div>
                    </div>
                </div>
                <div class="row border-top pt-3">
                    <div class="col-6">
                        @php
                            $work_start_date = old(
                                'work_start',
                                isset($worksheet) && $worksheet->work_start
                                    ? $worksheet->work_start->format('Y-m-d')
                                    : '',
                            );
                            $work_start_time = old(
                                'work_start_hour',
                                isset($worksheet) && $worksheet->work_start
                                    ? $worksheet->work_start->format('H:i')
                                    : '',
                            );
                        @endphp

                        <div class="input-group mb-3">
                            <label for="work_start" class="input-group-text w-30">Munka kezdete</label>
                            <button class="btn btn-outline-secondary time-btn" type="button"
                                id="work_start-btn">Most</button>
                            <input id="work_start" class="form-control" type="date" name="work_start"
                                value="{{ $work_start_date }}" />
                            <input id="work_start_hour" class="form-control" type="time" name="work_start_hour"
                                value="{{ $work_start_time }}" />
                        </div>
                        @php
                            $work_end_date = old(
                                'work_end',
                                isset($worksheet) && $worksheet->work_end ? $worksheet->work_end->format('Y-m-d') : '',
                            );
                            $work_end_time = old(
                                'work_end_hour',
                                isset($worksheet) && $worksheet->work_end ? $worksheet->work_end->format('H:i') : '',
                            );
                        @endphp

                        <div class="input-group mb-3">
                            <label for="work_end" class="input-group-text w-30">Munka vége</label>
                            <button class="btn btn-outline-secondary time-btn" type="button"
                                id="work_end-btn">Most</button>
                            <input id="work_end" class="form-control" type="date" name="work_end"
                                value="{{ $work_end_date }}" />
                            <input id="work_end_hour" class="form-control" type="time" name="work_end_hour"
                                value="{{ $work_end_time }}" />
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="work_time" name="work_time" type="number"
                                placeholder="30 perces egység"
                                value="{{ old('work_time', $worksheet->work_time ?? '') }}" />
                            <label for="work_time">30 perces egység</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Egyéb komment" id="comment" name="comment">{{ old('comment', $worksheet->comment ?? '') }}</textarea>
                            <label for="comment">Egyéb komment</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Hibaleírás" id="error_description" name="error_description">{{ old('error_description', $worksheet->error_description ?? '') }}</textarea>
                            <label for="error_description">Hibaleírás</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Elvégzett munka" id="work_description" name="work_description">{{ old('work_description', $worksheet->work_description ?? '') }}</textarea>
                            <label for="work_description">Elvégzett munka</label>
                        </div>
                    </div>
                </div>
                <div class="row border-top pt-3">
                    <div class="col-12">
                        @php
                            $isOutsourcingChecked = old(
                                'outsourcing',
                                !isset($worksheet) || isset($worksheet->outsourcing),
                            );
                        @endphp
                        <div class="form-check form-switch fs-4 mb-3">
                            <input type="hidden" name="outsourcing" value="0">
                            <input type="checkbox" class="form-check-input" role="switch" name="outsourcing"
                                id="outsourcing-switch" value="1" {{ $isOutsourcingChecked ? 'checked' : '' }}
                                @disabled(isset($worksheet->outsourcing))>
                            <label for="outsourcing-switch" class="form-check-label">Külső szervízeltetés</label>
                        </div>
                    </div>
                </div>
                <div class="row" id='outsourcing-row'>
                    <div class="col-6">
                        <div class="form-floating mb-3">
                            <select class="form-select" id="partner_id" name="partner_id">
                                @foreach ($companies->where('type', 'partner') as $company)
                                    <option value="{{ $company->id }}" id="company-id-{{ $company->id }}">
                                        {{ $company->name }}</option>
                                @endforeach
                            </select>
                            <label for="partner_id">Partner cég</label>
                        </div>
                        @php
                            $entry_date = old(
                                'entry_time',
                                isset($worksheet->outsourcing) && $worksheet->outsourcing->entry_time
                                    ? $worksheet->outsourcing->entry_time->format('Y-m-d')
                                    : '',
                            );
                            $entry_time = old(
                                'entry_time_hour',
                                isset($worksheet->outsourcing) && $worksheet->outsourcing->entry_time
                                    ? $worksheet->outsourcing->entry_time->format('H:i')
                                    : '',
                            );
                        @endphp

                        <div class="input-group mb-3">
                            <label for="entry_time" class="input-group-text w-30">Beviteli idő</label>
                            <button class="btn btn-outline-secondary time-btn" type="button"
                                id="entry_time-btn">Most</button>
                            <input id="entry_time" class="form-control" type="date" name="entry_time"
                                value="{{ $entry_date }}" />
                            <input id="entry_time_hour" class="form-control" type="time" name="entry_time_hour"
                                value="{{ $entry_time }}" />
                        </div>
                        @php
                            $selected_finished = old('finished', $worksheet->outsourcing->finished ?? '');
                        @endphp
                        <div class="form-floating">
                            <select class="form-select" id="finished" name="finished">
                                <option value="ongoing" {{ $selected_finished === 'ongoing' ? 'selected' : '' }}>
                                    Munka alatt</option>
                                <option value="finished" {{ $selected_finished === 'finished' ? 'selected' : '' }}>
                                    Elkészült</option>
                                <option value="brought" {{ $selected_finished === 'brought' ? 'selected' : '' }}>
                                    Elhozva</option>
                            </select>
                            <label for="finished">Külső státusz</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="outsourced_number" name="outsourced_number"
                                type="text" placeholder="Külső munkalapszám" @disabled(isset($worksheet->outsourcing))
                                value="{{ old('outsourced_number', $worksheet->outsourcing->outsourced_number ?? '') }}" />
                            <label for="outsourced_number">Külső munkalapszám</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="outsourced_price" name="outsourced_price" type="number"
                                placeholder="Vállalt árajánlat"
                                value="{{ old('outsourced_price', $worksheet->outsourcing->outsourced_price ?? '') }}" />
                            <label for="outsourced_price">Vállalt árajánlat</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="our_price" name="our_price" type="number"
                                placeholder="Saját árajánlat"
                                value="{{ old('our_price', $worksheet->outsourcing->our_price ?? '') }}" />
                            <label for="our_price">Saját árajánlat</label>
                        </div>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row mx-1 mt-5">
                    <button class="btn btn-primary btn-lg" type="submit" id="submit-btn">
                        @if (isset($worksheet))
                            Szerkesztés
                        @else
                            Létrehozás
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('css')
    <link rel="stylesheet" href="{{ asset('css/wideform.css') }}">
    <style>
        .w-30 {
            width: 30%;
        }
    </style>
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/worksheet_form.js') }}"></script>
@endpush
