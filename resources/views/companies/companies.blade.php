@isset($companies)
    <div class="modal fade" id="customer-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="customer-modal-label">Új ügyfél felvétele</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-body" data-request-url="{{ route('customer.store') }}"
                    data-csrf-token="{{ csrf_token() }}">
                    <input type="hidden" name="company" id="form-company">
                    <input type="hidden" name="company" id="form-customer">
                    <div class="mb-3">
                        <label for="name" class="form-label">Név</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email cím</label>
                        <input type="email" class="form-control" id="email" placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefonszám</label>
                        <input type="text" class="form-control" id="phone" placeholder="+12-34-567-8901">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="modal-send">Hozzáadás</button>
                </div>
            </div>
        </div>
    </div>
    <div class="company-wrapper">
        <div class="company-content">
            @foreach ($companies as $key => $value)
                <div class="company-container bg-light rounded-5">
                    <div class="company-col">
                        <h5>
                            <span class="badge rounded-pill text-bg-primary company-pill">
                                {{ $key == 'partner' ? 'Partnerek' : 'Ügyfelek' }}
                            </span>
                            <a class="btn badge rounded-pill text-bg-primary company-pill"
                                href="{{ route('company.create', ['type' => $key]) }}">
                                ➕
                            </a>
                        </h5>
                        <div class="company-card-container">
                            @foreach ($companies[$key] as $company)
                                <div class="company-card">
                                    <div class="row">
                                        <div class="col-12">
                                            <h3>{{ $company->name }}</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">{{ $company->post_code }}</div>
                                        <div class="col-6">{{ $company->city }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">{{ $company->street }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">{{ $company->phone }}</div>
                                        <div class="col-6">{{ $company->email }}</div>
                                    </div>
                                    @if ($key != 'partner')
                                        <div class="row my-2">
                                            <div class="accordion accordion-flush" id="accordion-{{ $company->id }}">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#accordion-collapse-{{ $company->id }}">
                                                            Ügyfelek
                                                        </button>
                                                    </h2>
                                                    <div id="accordion-collapse-{{ $company->id }}"
                                                        class="accordion-collapse collapse"
                                                        data-bs-parent="#accordion-{{ $company->id }}">
                                                        <div class="accordion-body">
                                                            @foreach ($company->customers as $customer)
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <b
                                                                            id="customer-name-{{ $customer->id }}">{{ $customer->name }}</b>
                                                                    </div>
                                                                    <div class="col-2"><button
                                                                            class="btn btn-success edit-customer-btn"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#customer-modal"
                                                                            id="edit-customer-{{ $customer->id }}-{{ $company->id }}">📝</button>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <form
                                                                            action="{{ route('customer.destroy', $customer->id) }}"
                                                                            method="post">
                                                                            @csrf
                                                                            @method('delete')
                                                                            <input type="submit"
                                                                                class="btn btn-danger edit-customer-btn"
                                                                                value="✖️">
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-xl-5 col-12"
                                                                        id="customer-phone-{{ $customer->id }}">
                                                                        {{ $customer->mobile }}</div>
                                                                    <div class="col-xl-7 col-12"
                                                                        id="customer-email-{{ $customer->id }}">
                                                                        {{ $customer->email }}</div>
                                                                </div>
                                                                <hr>
                                                            @endforeach
                                                            <button class="btn btn-success new-customer-btn"
                                                                data-bs-toggle="modal" data-bs-target="#customer-modal"
                                                                id="new-customer-{{ $company->id }}">➕</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row py-2">
                                        <div class="col-6">
                                            <a class="btn btn-success"
                                                href="{{ route('company.edit', $company->id) }}">Szerkesztés</a>
                                        </div>
                                        <div class="col-6">
                                            <form action="{{ route('company.destroy', $company->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger">Kapcsolat megszűntetése</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endisset

@push('css')
    <link rel="stylesheet" href="{{ asset('css/companies.css') }}">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/customer.js') }}"></script>
@endpush
