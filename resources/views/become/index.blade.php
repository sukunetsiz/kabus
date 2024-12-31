@extends('layouts.app')

@section('content')
<div class="container become-vendor-container">
    <div class="become-vendor-card">
        <h1 class="become-vendor-title">Become a Vendor</h1>
        
        <p class="become-vendor-text">You can also sell on Kabus Market! All you need to do is pay the vendor fee and inform us about the products you will sell. You can contact us by opening a support ticket or sending a direct message to yelpazesiz.</p>
        
        <p class="become-vendor-text">Before making this purchase, make sure you have thoroughly read <a href="{{ route('rules') }}" class="become-vendor-link">Kabus Market's rules</a>. We have zero tolerance for prohibited products. The purchase process is instant and irreversible, no refunds will be made. If you accept these terms and site rules, welcome to Kabus Market!</p>

        <a href="{{ route('become.payment') }}" class="btn btn-primary become-vendor-btn">Continue to Payment</a>
    </div>
</div>

@endsection