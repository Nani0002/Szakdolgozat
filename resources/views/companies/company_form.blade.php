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
                        <div class="form-floating mb-3">
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" placeholder="Név"
                                value="{{ old('name', $company->name ?? '') }}" />
                            <label for="name">Név</label>
                            @error('name')
                                <div class="invalid-feedback">
                                    Név megadása kötelező
                                </div>
                            @enderror
                        </div>

                        @if (isset($company))
                            <div class="form-floating mb-3">
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" disabled>
                                    <option value="{{ $company->type }}">
                                        {{ $company->type == 'partner' ? 'Partner' : 'Ügyfél' }}
                                    </option>
                                </select>
                                <label for="type">Cég típusa</label>
                                @error('type')
                                    <div class="invalid-feedback">
                                        Nem megfelelő típus
                                    </div>
                                @enderror
                            </div>
                        @else
                            <div class="form-floating mb-3">
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" name="type">
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
                                @error('type')
                                    <div class="invalid-feedback">
                                        Nem megfelelő típus
                                    </div>
                                @enderror
                            </div>
                        @endif

                        <div class="fs-4">Elhelyezkedés
                            <hr>
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control @error('post_code') is-invalid @enderror" id="post_code" name="post_code" type="text"
                                placeholder="Irányítószám" value="{{ old('post_code', $company->post_code ?? '') }}" />
                            <label for="post_code">Irányítószám</label>
                            @error('post_code')
                                <div class="invalid-feedback">
                                    Irányítószám megadása kötelező
                                </div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control @error('city') is-invalid @enderror" id="city" name="city" type="text" placeholder="Város"
                                value="{{ old('city', $company->city ?? '') }}" />
                            <label for="city">Város</label>
                            @error('city')
                                <div class="invalid-feedback">
                                    Város megadása kötelező
                                </div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control @error('street') is-invalid @enderror" id="street" name="street" type="text" placeholder="Utca"
                                value="{{ old('street', $company->street ?? '') }}" />
                            <label for="street">Utca</label>
                            @error('street')
                                <div class="invalid-feedback">
                                    Utca megadása kötelező
                                </div>
                            @enderror
                        </div>

                        <div class="fs-4">Elérhetési módok
                            <hr>
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" type="text"
                                placeholder="Telefonszám" value="{{ old('phone', $company->phone ?? '') }}" />
                            <label for="phone">Telefonszám</label>
                            @error('phone')
                                <div class="invalid-feedback">
                                    Telefonszám megadása kötelező
                                </div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email"
                                placeholder="Email cím" value="{{ old('email', $company->email ?? '') }}" />
                            <label for="email">Email cím</label>
                            @error('email')
                                <div class="invalid-feedback">
                                    Email cím megadása kötelező
                                </div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" type="submit">
                                @if (isset($company))
                                    Szerkesztés
                                @else
                                    Létrehozás
                                @endif
                            </button>
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
