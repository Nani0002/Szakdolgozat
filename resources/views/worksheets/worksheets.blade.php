@isset($worksheets)
    <div class="worksheet-frame bg-light rounded-5">
        @foreach ($worksheetTypes as $worksheetType)
            <div class="worksheet-column" id="worksheet-column-{{ $worksheetType }}">
                @switch($worksheetType)
                    @case('open')
                        <h5><span class="badge rounded-pill text-bg-primary worksheet-pill">Felvéve</span></h5>
                    @break

                    @case('started')
                        <h5><span class="badge rounded-pill text-bg-secondary worksheet-pill">Kiosztva</span></h5>
                    @break

                    @case('ongoing')
                        <h5><span class="badge rounded-pill text-bg-success worksheet-pill">Folyamatban</span></h5>
                    @break

                    @case('price_offered')
                        <h5><span class="badge rounded-pill text-bg-info worksheet-pill">Árajánlat kiadva</span></h5>
                    @break

                    @case('waiting')
                        <h5><span class="badge rounded-pill text-bg-dark worksheet-pill">Külsősre várunk</span></h5>
                    @break

                    @case('to_invoice')
                        <h5><span class="badge rounded-pill text-bg-warning worksheet-pill">Számlázni</span></h5>
                    @break

                    @case('closed')
                        <h5><span class="badge rounded-pill text-bg-danger worksheet-pill">Lezárva</span></h5>
                    @break
                @endswitch
                <div class="worksheet-container">

                </div>
            </div>
        @endforeach
    </div>
@endisset

@push('css')
    <link rel="stylesheet" href="{{ asset('css/worksheets.css') }}">
@endpush
