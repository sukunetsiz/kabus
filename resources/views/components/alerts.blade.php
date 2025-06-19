@if(session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('status'))
    <div class="alert alert-status" role="alert">
        {{ session('status') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" role="alert">
        {{ session('error') }}
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info" role="alert">
        {{ session('info') }}
    </div>
@endif
