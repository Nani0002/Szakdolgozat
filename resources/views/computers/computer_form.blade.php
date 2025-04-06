<div class="form-frame bg-light rounded-5">
    <div class="form-column">
        <div class="fs-4 mb-1 fw-bold">{{ $title }}</div>
        <div class="form-container">
            <form
                action="
                @if (request()->is('computer/create')) {{ route('computer.store') }}
                @elseif(request()->is('computer/*/edit'))
                    {{ route('computer.update', $computer->id) }}
                @elseif(request()->is('extra/create'))
                    {{ route('extra.store') }}
                @else
                    {{ route('extra.update', $extra->id) }} @endif
            "
                method="POST">
                @isset($computer)
                    @method('put')
                @endisset
                @isset($extra)
                    @method('put')
                @endisset
                @if(request()->is('extra/*/edit') || request()->is('extra/create'))
                <div class="form-floating mb-3">
                    <select  class="form-select" name="worksheet" id="worksheet" disabled>
                        <option value="{{$connected_worksheet->id}}">{{$connected_worksheet->sheet_number}}</option>
                    </select>
                    <label for="worksheet_id">Csatolt munkalap</label>
                    <input type="hidden" name="worksheet_id" value="{{$connected_worksheet->id}}">
                </div>
                <div class="form-floating mb-3">
                    <select  class="form-select" name="computer" id="computer" disabled>
                        <option value="{{$connected_computer->id}}">{{$connected_computer->serial_number}}</option>
                    </select>
                    <label for="computer_id">Csatolt számítógép</label>
                    <input type="hidden" name="computer_id" value="{{$connected_computer->id}}">
                </div>
                @endif
                @csrf
                <div class="form-floating mb-3">
                    <input class="form-control" id="manufacturer" name="manufacturer" type="text" placeholder="Gyártó"
                        value="{{ old('manufacturer', $computer->manufacturer ?? ($extra->manufacturer ?? '')) }}" />
                    <label for="manufacturer">Gyártó</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" id="type" name="type" type="text" placeholder="Típus"
                        value="{{ old('type', $computer->type ?? ($extra->type ?? '')) }}" />
                    <label for="type">Típus</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" id="serial_number" name="serial_number" type="text"
                        placeholder="Sorozatszám"
                        value="{{ old('serial_number', $computer->serial_number ?? ($extra->serial_number ?? '')) }}"
                        @disabled(isset($computer) || isset($extra)) />
                    <label for="serial_number">Sorozatszám</label>
                </div>
                <div class="container">
                    <div class="row">
                        <button class="btn btn-primary btn-lg" type="submit" id="submit-btn">
                            @if (isset($computer) || isset($extra))
                                Szerkesztés
                            @else
                                Létrehozás
                            @endif
                        </button>
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
            </form>
        </div>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="{{ asset('css/thinform.css') }}">
@endpush
