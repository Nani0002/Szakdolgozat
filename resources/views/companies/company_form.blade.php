<div class="form-frame bg-light rounded-5">
    <div class="form-column">
        <div class="fs-4 mb-1 fw-bold">{{ isset($company) ? 'Ügyfél cég szerkesztése' : 'Ügyfél cég létrehozása' }}</div>
        <div class="form-container">
            <form action="{{ isset($company) ? route('company.update', $company->id) : route('company.store') }}"
                method="post">
                @csrf
                @isset($company)
                    @method('put')
                    <input type="hidden" name="id">
                @endisset

                <div class="container">
                    <form id="contactForm">
                        @error('name')
                            <span class="text-danger fw-light">Név megadása kötelező!</span>
                        @enderror
                        <div class="form-floating mb-3">
                            <input class="form-control" id="name" name="name" type="text" placeholder="Név"
                                value="{{ old('name', $company->name ?? '') }}" />
                            <label for="name">Név</label>
                        </div>

                        @error('type')
                            <span class="text-danger fw-light">Cég típus megadása kötelező!</span>
                        @enderror
                        @if (isset($company))
                            <div class="form-floating mb-3">
                                <select class="form-select" id="type" name="type" disabled>
                                    <option value="{{ $company->type }}">
                                        {{ $company->type == 'partner' ? 'Partner' : 'Ügyfél' }}
                                    </option>
                                </select>
                                <label for="type">Cég típusa</label>
                            </div>
                        @else
                            <div class="form-floating mb-3">
                                <select class="form-select" id="type" name="type" name="type">
                                    <option value="customer"
                                        {{ old('type', $type ?? 'customer') === 'customer' ? 'selected' : '' }}>
                                        Ügyfél
                                    </option>
                                    <option value="partner"
                                        {{ old('type', $type ?? 'customer') === 'partner' ? 'selected' : '' }}>
                                        Partner
                                    </option>
                                </select>
                                <label for="type">Cég típusa</label>
                            </div>
                        @endif

                        <div class="fs-4">Elhelyezkedés
                            <hr>
                        </div>

                        @error('post_code')
                            <span class="text-danger fw-light">Irányítószám megadása kötelező!</span>
                        @enderror
                        <div class="form-floating mb-3">
                            <input class="form-control" id="post_code" name="post_code" type="text" placeholder="Irányítószám"
                                value="{{ old('post_code', $company->post_code ?? '') }}" />
                            <label for="post_code">Irányítószám</label>
                        </div>

                        @error('city')
                            <span class="text-danger fw-light">Város megadása kötelező!</span>
                        @enderror
                        <div class="form-floating mb-3">
                            <input class="form-control" id="city" name="city" type="text" placeholder="Város"
                                value="{{ old('city', $company->city ?? '') }}" />
                            <label for="city">Város</label>
                        </div>

                        @error('street')
                            <span class="text-danger fw-light">Utca megadása kötelező!</span>
                        @enderror
                        <div class="form-floating mb-3">
                            <input class="form-control" id="street" name="street" type="text" placeholder="Utca"
                                value="{{ old('street', $company->street ?? '') }}" />
                            <label for="street">Utca</label>
                        </div>

                        <div class="fs-4">Elérhetési módok
                            <hr>
                        </div>

                        @error('phone')
                            <span class="text-danger fw-light">Céges telefonszám megadása kötelező!</span>
                        @enderror
                        <div class="form-floating mb-3">
                            <input class="form-control" id="phone" name="phone" type="text" placeholder="Telefonszám"
                                value="{{ old('phone', $company->phone ?? '') }}" />
                            <label for="phone">Telefonszám</label>
                        </div>

                        @error('email')
                            <span class="text-danger fw-light">Céges email cím megadása kötelező!</span>
                        @enderror
                        <div class="form-floating mb-3">
                            <input class="form-control" id="email" name="email" type="email" placeholder="Email cím"
                                value="{{ old('email', $company->email ?? '') }}" />
                            <label for="email">Email cím</label>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="{{ asset('css/thinform.css') }}">
@endpush
