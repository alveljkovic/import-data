@extends('adminlte::page')

@section('title', 'Permissions & Roles Management')

@section('content_header')
    <h1>Permissions & Roles Management</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="row">
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Create Permission</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('permissions.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Permission Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. user-management" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-info btn-block">Create Permission</button>
                    </form>
                </div>
                <div class="card-footer p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>All Defined Permissions</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions->sortBy('name') as $permission)
                                <tr>
                                    <td><span class="badge badge-success">{{ $permission->name }}</span></td>
                                    <td>
                                        <a href="{{ route('permissions.edit', $permission) }}" 
                                        class="btn btn-xs btn-default text-primary shadow" title="Edit Permission">
                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                        </a>
                                        
                                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete the permission: {{ $permission->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-default text-danger shadow" title="Delete Permission">
                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Assign Permissions/Roles to Users</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles/Permissions</th>
                                <th style="width: 100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-info">{{ $role->name }} (Role)</span>
                                        @endforeach
                                        @foreach($user->getDirectPermissions() as $permission)
                                            <span class="badge badge-warning">{{ $permission->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('permissions.assign.edit', $user) }}" class="btn btn-xs btn-default text-primary shadow" title="Assign Permissions">
                                            <i class="fa fa-lg fa-fw fa-shield-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@stop