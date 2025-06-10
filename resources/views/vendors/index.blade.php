@extends('layouts.app')

@section('content')

<div class="vendors-index-container">
    <div class="vendors-index-card">
        <h1 class="vendors-index-title">Vendor List</h1>
        @if($vendors->isEmpty())
            <p class="vendors-index-empty">No vendors found.</p>
        @else
            <div class="vendors-index-grid">
                @foreach($vendors as $vendor)
                    <a href="{{ route('vendors.show', $vendor->username) }}" class="vendors-index-item">
                        <div class="vendors-index-avatar">
                            <img src="{{ $vendor->profile ? $vendor->profile->profile_picture_url : asset('images/default-profile-picture.png') }}" 
                                 alt="{{ $vendor->username }}'s Profile Picture">
                        </div>
                        <span class="vendors-index-username">{{ $vendor->username }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
