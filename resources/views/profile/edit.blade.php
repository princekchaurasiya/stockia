@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="container-fluid">
        <x-ui.page-header title="Profile" />

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        @php
                            $avatarUrl = $user->avatar_path
                                ? asset('storage/' . $user->avatar_path)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D6EFD&color=fff';
                        @endphp
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle mb-3" width="96" height="96">
                        <p class="mb-0 fw-semibold">{{ $user->name }}</p>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name"
                                       value="{{ old('name', $user->name) }}"
                                       class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email"
                                       value="{{ old('email', $user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="avatar" class="form-label">Profile image</label>
                                <input type="file" id="avatar" name="avatar"
                                       class="form-control @error('avatar') is-invalid @enderror"
                                       accept="image/*">
                                @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">PNG/JPG up to 2 MB.</div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label for="password" class="form-label">New password</label>
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       autocomplete="new-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Leave blank to keep current password.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm new password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control"
                                       autocomplete="new-password">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Save changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

