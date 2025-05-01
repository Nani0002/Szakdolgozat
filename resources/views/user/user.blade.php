<div class="container">
    <div class="row mt-5">
        <div class="col-xl-10 offset-xl-1 shadow p-5 py-4 rounded-4 bg-light col-md-12">
            <div class="row">
                <div class="col-6 d-flex align-items-center">
                    <div class="position-relative d-inline-block me-2">
                        <img src="{{ Storage::url('images/' . $profile->imagename_hash) }}"
                             class="profile-img"
                             id="profile-img"
                             data-image-update-url="{{ route('user.new_image') }}"
                             data-csrf-token="{{ csrf_token() }}"
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">

                        <button class="btn btn-light rounded-circle plus-btn border-dark border-2 position-absolute"
                                id="changeImageBtn">
                            <b>+</b>
                        </button>
                    </div>

                    <div>
                        <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*">
                        <div class="invalid-feedback d-none">Nem megfelelő kép formátum!</div>
                    </div>
                </div>
                <div class="col-6 profile-name ps-5">{{ $profile->name }}</div>

            </div>

            <div class="row mt-5">
                <form action="{{ route('user.new_password') }}" method="post">
                    @csrf

                    <div class="mb-3 form-floating">
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Jelszó">
                        <label for="password">Jelszó</label>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="my-3 form-floating">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" placeholder="Jelszó mégegyszer">
                        <label for="password_confirmation">Jelszó mégegyszer</label>
                    </div>

                    <div class="row mt-4">
                        <div class="col-3 d-grid">
                            <input class="btn btn-danger" type="submit" value="Jelszó megváltoztatása">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/profile.js') }}"></script>
    <script src="{{ asset('js/handleAjaxErrors.js') }}"></script>
@endpush
