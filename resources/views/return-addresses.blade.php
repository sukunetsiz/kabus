@extends('layouts.app')

@section('content')

<div class="return-addresses-container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="return-addresses-disclaimer">
       To shop at {{ config('app.name') }} or play Moneta, you need to add at least one Monero address. Refunds will be made to this address. For your security, use a subaddress instead of your main address and be careful not to share this address elsewhere. Main Monero addresses usually start with "4", while subaddresses start with "8".
    </div>
    
    <div class="return-addresses-card">
        <form action="{{ route('return-addresses.store') }}" method="POST">
            @csrf
            <div class="return-addresses-form-group">
                <input type="text" 
                       class="return-addresses-input @error('monero_address') is-invalid @enderror" 
                       id="monero_address" 
                       name="monero_address" 
                       placeholder="Enter your Monero refund address"
                       required>
                @error('monero_address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
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
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnAddresses as $address)
                            <tr>
                                <td>{{ $address->monero_address }}</td>
                                <td>
                                    <span class="{{ $address->is_verified ? 'return-addresses-verified' : 'return-addresses-unverified' }}">
                                        {{ $address->is_verified ? 'Verified' : 'Unverified' }}
                                    </span>
                                </td>
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
