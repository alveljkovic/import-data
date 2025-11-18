@extends('adminlte::page')

@section('title', 'Imports')

@section('content_header')
    <h1>Imports</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Import Type</th>
                        <th>File Key</th>
                        <th>Original Filename</th>
                        <th>Status</th>
                        <th style="width: 80px;">Logs</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($imports as $import)
                        <tr>
                            <td>{{ $import->user->name }}</td>
                            <td>{{ $import->import_type_key }}</td>
                            <td>{{ $import->file_config_key }}</td>
                            <td>{{ $import->original_filename }}</td>
                            <td>
                                @if($import->status === 'successful')
                                    <span class="badge badge-success">Successful</span>
                                @else
                                    <span class="badge badge-danger">Unsuccessful</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info show-import-logs-btn"
                                        data-log-id="{{ $import->id }}" title="View Log">
                                    <i class="fa fa-history"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No imports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="card-footer">
                {{ $imports->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="importLogsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="importLogsModalTitle">Error Logs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="importLogsModalContent">
                    <p class="text-center text-muted">Loading logs...</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@stop

@push('js')
    @vite('resources/js/imports.js')
@endpush
