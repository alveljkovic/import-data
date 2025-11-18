@extends('adminlte::page')

@section('title', 'Assign Permissions to User')

@section('content_header')
    <h1>Assign Permissions to: **{{ $user->name }}**</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Update Permissions and Roles</h3>
        </div>
        
        <form action="{{ route('permissions.assign.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="form-group">
                    <label>Assigned Roles</label>
                    <div class="row">
                        @forelse ($roles->sortBy('name') as $role)
                            <div class="col-sm-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                           class="custom-control-input" id="role-{{ $role->id }}" 
                                           {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="role-{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted col-12">No Roles defined.</p>
                        @endforelse
                    </div>
                </div>

                <hr>

                {{-- SEKCIJA DIREKTNIH PERMISIJA --}}
                <div class="form-group">
                    <label>Assigned Direct Permissions</label>
                    <div class="row">
                        @forelse ($permissions->sortBy('name') as $permission)
                            <div class="col-sm-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                           class="custom-control-input" id="perm-{{ $permission->id }}" 
                                           {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="perm-{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted col-12">No Direct Permissions defined.</p>
                        @endforelse
                    </div>
                </div>
                
            </div>
            
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save Permissions</button>
                <a href="{{ route('permissions.index') }}" class="btn btn-default">Back to List</a>
            </div>
        </form>
    </div>
@stop