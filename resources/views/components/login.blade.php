<div class="row mt-5">
    <div class="col-xl-6 offset-xl-3 shadow p-5 py-4 rounded-4 bg-light col-md-8 offset-md-2">
        <div class="row mb-4 fs-5 fw-bold">Bejelentkezés</div>
        <div class="row">
            <form action="{{route('login')}}" method="POST">
                @csrf
                <div class="mb-3 form-floating">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control"
                        placeholder="Email cím">
                    <label for="email">Email cím</label>
                </div>

                <div class="mb-3 form-floating">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Jelszó">
                    <label for="password">Jelszó</label>
                </div>

                <div class="row mt-4">
                    <div class="col-3 offset-8 d-grid">
                        <input class="btn btn-success" type="submit" value="Bejelentkezés">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
