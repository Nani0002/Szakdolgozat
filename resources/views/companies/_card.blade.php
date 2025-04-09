<div id="customer-{{ $customer->id }}">
    <div class="row">
        <div class="col-8">
            <b id="customer-name-{{ $customer->id }}">{{ $customer->name }}</b>
        </div>
        <div class="col-2"><button class="btn btn-success edit-customer-btn" data-bs-toggle="modal"
                data-bs-target="#customer-modal" id="edit-customer-{{ $customer->id }}-{{ $company->id }}">📝</button>
        </div>
        <div class="col-2">
            <form action="{{ route('customer.destroy', $customer->id) }}" method="post">
                @csrf
                @method('delete')
                <input type="submit" class="btn btn-danger edit-customer-btn" value="✖️">
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-5 col-12" id="customer-phone-{{ $customer->id }}">
            {{ $customer->mobile }}</div>
        <div class="col-xl-7 col-12" id="customer-email-{{ $customer->id }}">
            {{ $customer->email }}</div>
    </div>
    <hr>
</div>
