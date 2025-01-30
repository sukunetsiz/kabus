@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Create New Pop-up</h3>
                        <a href="{{ route('admin.popup.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.popup.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}" 
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="5" 
                                          required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" 
                                       class="form-check-input @error('active') is-invalid @enderror" 
                                       id="active" 
                                       name="active" 
                                       value="1" 
                                       {{ old('active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">
                                    Make this pop-up active
                                </label>
                                <div class="form-text">
                                    Note: Only one pop-up can be active at a time. Activating this will deactivate any currently active pop-up.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Pop-up</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
