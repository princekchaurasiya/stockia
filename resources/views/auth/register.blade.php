@extends('layouts.app')

@section('title', __('stockia.app.nav.register'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header">{{ __('stockia.auth.register') }}</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('stockia.auth.name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('stockia.auth.email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('stockia.auth.password') }}</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('stockia.auth.password_confirmation') }}</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('stockia.auth.register') }}</button>
                        <p class="mt-3 mb-0 text-muted small">
                            {{ __('stockia.auth.have_account') }}
                            <a href="{{ route('login') }}">{{ __('stockia.auth.login') }}</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
