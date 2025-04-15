<div class="container">
    <div class="row mt-5">
        <div class="col-xl-10 offset-xl-1 shadow p-5 py-4 rounded-4 bg-light col-md-12">
            <div class="row">
                <div class="col-1 img-div">
                    <img src='{{ Storage::url('images/' . $profile->imagename_hash) }}' class="profile-img"
                        id="profile-img" data-image-update-url="{{ route('user.new_image') }}"
                        data-csrf-token="{{ csrf_token() }}">
                    <div class="form-group">
                        <input type="file" id="imageUpload" name="imageUpload" class="d-none" accept="image/*">
                        <div class="invalid-feedback d-none">Nem megfelelő kép formátum!</div>
                    </div>
                    <button class="btn btn-light rounded-circle plus-btn border-dark border-2"
                        id="changeImageBtn"><b>+</b></button>
                </div>
                <div class="col-11 profile-name ps-5">{{ $profile->name }}</div>
            </div>
            <div class="row mt-5">
                <form action="{{ route('user.new_password') }}" method="post">
                    @csrf
                    @error('password')
                        <span class="text-danger fw-light">{{ $errors->get('password')[0] }}</span>
                    @enderror
                    <div class="mb-3 form-floating">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Jelszó">
                        <label for="password">Jelszó</label>
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
