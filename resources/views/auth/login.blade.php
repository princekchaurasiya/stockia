@extends('layouts.app')

@section('title', __('stockia.app.nav.login'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header">{{ __('stockia.auth.login') }}</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('stockia.auth.email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('stockia.auth.password') }}</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">{{ __('stockia.auth.remember') }}</label>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('stockia.auth.login') }}</button>
                        <p class="mt-3 mb-0 text-muted small">
                            {{ __('stockia.auth.no_account') }}
                            <a href="{{ route('register') }}">{{ __('stockia.auth.register') }}</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
