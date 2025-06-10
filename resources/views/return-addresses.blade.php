@extends('layouts.app')

@section('content')

<div class="return-addresses-container">
    <div class="return-addresses-disclaimer">
       To shop at {{ config('app.name') }}, you need to add at least one Monero address. Refunds will be made to this address. For your security, use a subaddress instead of your main address and be careful not to share this address elsewhere. Main Monero addresses usually start with "4", while subaddresses start with "8".
    </div>
    
    <div class="return-addresses-card">
        <form action="{{ route('return-addresses.store') }}" method="POST">
            @csrf
            <div class="return-addresses-form-group">
                <input type="text" 
                       class="return-addresses-input" 
                       id="monero_address" 
                       name="monero_address" 
                       placeholder="Enter your Monero refund address"
                       required
                       minlength="40"
                       maxlength="160">
            </div>
            <div class="return-addresses-submit-container">
                <button type="submit" class="return-addresses-submit-btn">Add Monero Address</button>
            </div>
        </form>
    </div>

    @if($returnAddresses->count() > 0)
        <div class="return-addresses-card">
            <div class="return-addresses-table-container">
                <table class="return-addresses-table">
                    <thead>
                        <tr>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnAddresses as $address)
                            <tr>
                                <td>{{ $address->monero_address }}</td>
                                <td>
                                    <form action="{{ route('return-addresses.destroy', $address) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="return-addresses-delete-btn">
                                        Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="return-addresses-empty">
            You haven't added any refund addresses yet.
        </div>
    @endif
</div>
@endsection
