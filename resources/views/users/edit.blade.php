@extends('adminlte::page')

@section('title', isset($user) ? 'Edit user' : 'Create user')

@section('content_header')
    <h1>{{ isset($user) ? 'Edit user: ' . $user->name : 'Create user' }}</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Data</h3>
        </div>
        
        <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label for="name">Full name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ isset($user) ? '' : 'required' }}>
                    <small class="form-text text-muted">{{ isset($user) ? 'Leave it blank if you do not want to change password' : '' }}</small>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control" {{ isset($user) ? '' : 'required' }}>
                </div>
                
            </div>
            
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
@stop