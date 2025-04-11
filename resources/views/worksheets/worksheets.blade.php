@isset($worksheets)
    <div class="worksheet-frame bg-light rounded-5" data-update-url="{{ route('worksheet.move') }}"
        data-csrf-token="{{ csrf_token() }}" id="dragdrop-frame">
        @foreach ($worksheetTypes as $worksheetType => $worksheetTypePreview)
            <div class="worksheet-column" id="worksheet-column-{{ $worksheetType }}">
                <h5>
                    <span
                        class="badge rounded-pill text-bg-{{ $worksheetTypePreview['color'] }} worksheet-pill">{{ $worksheetTypePreview['text'] }}</span>
                </h5>
                <div class="worksheet-container dragdrop-container accordion" id="worksheet-container-{{ $worksheetType }}"
                    ondrop="drop(event)" ondragover="allowDrop(event)">
                    @foreach ($worksheets[$worksheetType] as $worksheet)
                        <div class="accordion-item" draggable="true" ondragstart="drag(event)"
                            id="accordion-{{ $worksheet->id }}" data-slot="{{ $worksheet->slot_number }}"
                            data-delete-url="{{ route('worksheet.destroy', ['worksheet' => $worksheet->id]) }}"
                            data-close-url="{{ route('worksheet.close', ['worksheet' => $worksheet->id]) }}"
                            @if ($worksheet->final) data-final="true" @endif>
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $worksheet->id }}" aria-expanded="true"
                                    aria-controls="collapse{{ $worksheet->id }}">
                                    {{ $worksheet->sheet_number }}
                                    @if ($user_id == $worksheet['liable_id'])
                                        ❗
                                    @endif
                                </button>
                            </h2>
                            <div id="collapse{{ $worksheet->id }}" class="accordion-collapse collapse"
                                data-bs-parent="#worksheet-container-{{ $worksheetType }}">
                                <div class="accordion-body">
                                    <div class="fw-bold">
                                        {{ $worksheet->customer->company->name }} / <br> {{ $worksheet->customer->name }}
                                    </div>
                                    <div class="mt-2">
                                        {{ $worksheet->error_description }}
                                    </div>
                                    @if (isset($worksheet->computers) && count($worksheet->computers) > 0)
                                        <div class="fw-bold">Számítógépek: {{ count($worksheet->computers) }} db</div>
                                    @endif
                                    @isset($worksheet->outsourcing)
                                        <div class="fw-bold mt-2">Külsős munka</div>
                                    @endisset
                                    <div class="row mt-2">
                                        <div class="col-5">
                                            <a href="{{ route('worksheet.show', $worksheet->id) }}"
                                                class="btn btn-info">Részletek</a>
                                        </div>
                                        <div class="col-6">
                                            @if ($worksheetType != 'closed')
                                                <form
                                                    action="{{ route('worksheet.close', ['worksheet' => $worksheet->id]) }}"
                                                    method="POST" id="close-form-{{ $worksheet->id }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="submit" value="Lezárás" class="btn btn-danger ms-3 mb-3"
                                                        id="close-btn-{{ $worksheet->id }}">
                                                </form>
                                            @else
                                                <form
                                                    action="{{ route('worksheet.destroy', ['worksheet' => $worksheet->id]) }}"
                                                    method="POST" id="close-form-{{ $worksheet->id }}">
                                                    @csrf
                                                    @method('delete')
                                                    <input type="submit" value="Törlés" class="btn btn-danger ms-3 mb-3"
                                                        id="close-btn-{{ $worksheet->id }}">
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="accordion-item p-3">
                        <a href="{{ route('worksheet.create', ['current_step' => $worksheetType]) }}" draggable="false"
                            class="d-flex plus-btn">+</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endisset

@push('css')
    <link rel="stylesheet" href="{{ asset('css/worksheets.css') }}">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/dragdrop.js') }}"></script>
@endpush
