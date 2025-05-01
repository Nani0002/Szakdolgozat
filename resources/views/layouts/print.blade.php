<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print - {{ $worksheet->sheet_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        @media print {

            html,
            body {
                height: auto;
            }

            .print-signature-wrapper {
                page-break-inside: avoid;
                margin-top: auto;
                padding-top: 2rem;
                display: block;
            }

            #signature {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .container-fluid {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .wrapper {
                page-break-inside: avoid;
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>

<body class="p-2">
    @php
        switch ($worksheet->current_step) {
            case 'open':
                $status = 'Felvéve';
                break;
            case 'started':
                $status = 'Kiosztva';
                break;
            case 'ongoing':
                $status = 'Folyamatban';
                break;
            case 'price_offered':
                $status = 'Árajánlat kiadva';
                break;
            case 'waiting':
                $status = 'Külsősre várunk';
                break;
            case 'to_invoice':
                $status = 'Számlázni';
                break;
            case 'closed':
                $status = 'Lezárva';
                break;
            default:
                $status = '';
        }
        switch ($worksheet->declaration_mode) {
            case 'email':
                $mode = 'E-mailben';
                break;
            case 'phone':
                $mode = 'Telefonon';
                break;
            case 'personal':
                $mode = 'Személyesen';
                break;
            case 'onsite':
                $mode = 'Helyszíni';
                break;
        }
        switch ($worksheet->sheet_type) {
            case 'paid':
                $type = 'Fizetős';
                break;
            case 'maintanance':
                $type = 'Karbantartós';
                break;
            case 'warranty':
                $type = 'Garanciális';
                break;
        }
    @endphp
    <h1 class="text-center">Szervíz munkalap ({{ $status }})</h1>

    <div class="d-flex justify-content-between">
        <span>Nyomtatva: {{ $worksheet->print_date }}</span>
        <span>Bizonylatszám: <span class="fw-bold">{{ $worksheet->sheet_number }}</span></span>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-6 border border-dark border-2">
                <div class="row">
                    <div class="col-12 fw-bold text-decoration-underline">Kiállító:</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Név:</div>
                    <div class="col-9"></div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Cím:</div>
                    <div class="col-9"></div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Telefon:</div>
                    <div class="col-9"></div>
                </div>
            </div>
            <div class="col-6 border border-dark border-2">
                <div class="row">
                    <div class="col-12 fw-bold">Bejelentő cég:</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Név:</div>
                    <div class="col-9">{{ $worksheet->customer->company->name }}</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Cím:</div>
                    <div class="col-9">{{ $worksheet->customer->company->post_code }}
                        {{ $worksheet->customer->company->city }}</div>
                </div>
                <div class="row">
                    <div class="col-9 offset-3">{{ $worksheet->customer->company->street }}</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Telefon:</div>
                    <div class="col-9">{{ $worksheet->customer->company->phone }}</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">E-mail::</div>
                    <div class="col-9">{{ $worksheet->customer->company->email }} </div>
                </div>
            </div>
        </div>
        <div class="row border border-dark">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 fw-bold text-decoration-underline">Bejelentés:</div>
                </div>
                <div class="row mt-2">
                    <div class="col-3 fw-bold">Bejelentő személy:</div>
                    <div class="col-9">{{ $worksheet->customer->name }}</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Cím:</div>
                    <div class="col-9">{{ $worksheet->customer->company->post_code }}
                        {{ $worksheet->customer->company->city }} {{ $worksheet->customer->company->street }}</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Telefonszám:</div>
                    <div class="col-9">{{ $worksheet->customer->mobile }}</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Email:</div>
                    <div class="col-9">{{ $worksheet->customer->email }}</div>
                </div>
                <div class="row">
                    <div class="col-3 fw-bold">Bejelentés ideje:</div>
                    <div class="col-9">{{ $worksheet->declaration_time }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-3 fw-bold">Bejelentés módja:</div>
                    <div class="col-3">{{ $mode }}</div>
                    <div class="col-3 fw-bold">Javítás típusa:</div>
                    <div class="col-3">{{ $type }}</div>
                </div>
                @if (isset($worksheet->computers) && count($worksheet->computers) > 0)
                    <div class="wrapper">
                        <div class="row">
                            <div class="col-12 fw-bold text-decoration-underline">Beadott Számítógép@php echo count($worksheet->computers) > 1 ? 'ek:' : ':' @endphp
                            </div>
                        </div>
                        @foreach ($worksheet->computers as $computer)
                            <div class="row">
                                <div class="col-3 fw-bold">Sorozatzám:</div>
                                <div class="col-9">{{ $computer->serial_number }}</div>
                            </div>
                            <div class="row">
                                <div class="col-3 fw-bold">Gyártó:</div>
                                <div class="col-3">{{ $computer->manufacturer }}</div>
                                <div class="col-3 fw-bold">Típus:</div>
                                <div class="col-3">{{ $computer->type }}</div>
                            </div>
                            <div class="row">
                                <div class="col-3 fw-bold">Állapot:</div>
                                <div class="col-9">{{ $computer->pivot->condition }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="row mt-2">
                    <div class="col-6">Hiba / feladat leírása:</div>
                    <div class="col-6">Megjegyzés:</div>
                </div>
                <div class="row">
                    <div class="col-6 border border-dark">{{ $worksheet->error_description }}</div>
                    <div class="col-6 border border-dark">{{ $worksheet->comment }}</div>
                </div>
            </div>
        </div>
        <div class="row border border-dark pt-2 wrapper">
            <div class="col-6">
                <div class="row">
                    <div class="col-12 fw-bold text-decoration-underline">Belső munkaleírás:</div>
                </div>
                <div class="row mt-2">
                    <div class="col-6 fw-bold">Belső munkatárs:</div>
                    <div class="col-6">{{ $worksheet->coworker->name }}</div>
                </div>
                <div class="row">
                    <div class="col-6 fw-bold">Belső felelős:</div>
                    <div class="col-6">{{ $worksheet->liable->name }}</div>
                </div>
                <div class="row">
                    <div class="col-6 fw-bold">Munka kezdete:</div>
                    <div class="col-6">{{ $worksheet->work_start }}</div>
                </div>
                <div class="row">
                    <div class="col-6 fw-bold">Munka vége:</div>
                    <div class="col-6">{{ $worksheet->work_end }}</div>
                </div>
                <div class="row">
                    <div class="col-6 fw-bold">Munkaidő:</div>
                    <div class="col-6">{{ $worksheet->work_time }}</div>
                </div>
            </div>
            @isset($worksheet->work_description)
                <div class="col-6 d-flex align-items-stretch">
                    <div class="row">
                        <div class="col-5 fw-bold">Belső munkaleírás:</div>
                        <div class="col-7 d-flex justify-content-end">
                            <div class="border border-dark">{{ $worksheet->work_description }}</div>
                        </div>
                    </div>
                </div>
            @endisset

        </div>
        @if (isset($worksheet->outsourcing))
            <div class="wrapper">
                <div class="row border border-dark border-bottom-0 bg-secondary-subtle fw-bold">
                    <div class="col-4">Külső munkalap</div>
                    <div class="col-3">Külső árajánlat</div>
                    <div class="col-3">Saját ajánlatunk</div>
                    <div class="col-2">Státusz</div>
                </div>
                <div class="row border-bottom border-dark">
                    <div class="col-4">
                        {{ $worksheet->outsourcing->outsourced_number }}<br>{{ $worksheet->outsourcing->company->name }}
                    </div>
                    <div class="col-3">{{ $worksheet->outsourcing->outsourced_price }} Ft.</div>
                    <div class="col-3">{{ $worksheet->outsourcing->our_price }} Ft.</div>
                    <div class="col-2">
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
        @endif
        <div class="print-signature-wrapper">
            <div class="row mt-5 mx-2" id="signature">
                <div class="col-3 border-top border-2 border-dark text-center">
                    Átadó
                    @if (strlen($worksheet->customer->name) > 10)
                        <br>
                    @endif
                    ({{ $worksheet->customer->name }})
                </div>
                <div class="col-3 offset-6 border-top border-2 border-dark text-center">
                    Kiállító
                    @if (strlen($worksheet->liable->name) > 10)
                        <br>
                    @endif
                    ({{ $worksheet->liable->name }})
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
